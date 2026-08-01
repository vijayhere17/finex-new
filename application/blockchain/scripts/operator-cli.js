#!/usr/bin/env node
/**
 * FinexVault operator CLI — called from Laravel via shell_exec.
 * Usage: node operator-cli.js <command> '<json-args>'
 *
 * Commands:
 *   verifyInvest { hash }
 *   syncRoi { investmentId, totalRoiGenerated, roiDays }
 *   creditAutoUpgrade { sponsor, from, amount }
 *   autoUpgrade { user, offchainStakeId }
 *   processWithdrawal { user, grossAmount, offchainWithdrawalId }
 *   syncIncome { user, amount, incomeType }
 *   creditQualifyingDirect { sponsor, direct, packageAmount }
 *   recordInvestment { user, slotNumber, sponsor, offchainStakeId, pullFromTreasury }
 *   getMember { user }
 *   getInvestment { investmentId }
 *   getSponsorWallet { user }
 */
const fs = require('fs');
const path = require('path');
const { ethers } = require('ethers');

function loadConfig() {
  const abiPath = process.env.FINEX_ABI_PATH
    || path.join(__dirname, '..', 'abi', 'FinexVault.json');
  const payload = JSON.parse(fs.readFileSync(abiPath, 'utf8'));

  const rpc = process.env.BSC_RPC_URL
    || process.env.BSC_TESTNET_RPC
    || 'https://data-seed-prebsc-1-s1.binance.org:8545';
  const vaultAddress = process.env.FINEX_VAULT_ADDRESS || payload.address;
  const usdtAddress = process.env.BLOCKCHAIN_USDT_ADDRESS || payload.usdt;
  let key = process.env.BLOCKCHAIN_OPERATOR_KEY || process.env.WITHDRAWAL_PRIVATE_KEY || '';
  if (key && !key.startsWith('0x')) key = '0x' + key;

  return { abi: payload.abi, vaultAddress, usdtAddress, rpc, key };
}

function toWei(amount) {
  // amount is USD string/number with up to 4 decimals from Laravel
  return ethers.parseUnits(String(amount), 18);
}

function fromWei(wei) {
  return ethers.formatUnits(wei, 18);
}

function ok(data) {
  process.stdout.write(JSON.stringify({ success: true, ...data }));
}

function fail(message, extra = {}) {
  process.stdout.write(JSON.stringify({ success: false, error: message, ...extra }));
  process.exitCode = 1;
}

async function getContracts() {
  const cfg = loadConfig();
  if (!cfg.vaultAddress) throw new Error('FINEX_VAULT_ADDRESS not configured');
  const provider = new ethers.JsonRpcProvider(cfg.rpc);
  const wallet = cfg.key ? new ethers.Wallet(cfg.key, provider) : null;
  const runner = wallet || provider;
  const vault = new ethers.Contract(cfg.vaultAddress, cfg.abi, runner);
  return { cfg, provider, wallet, vault };
}

async function verifyInvest(args) {
  const { cfg, provider, vault } = await getContracts();
  const hash = args.hash;
  if (!hash) throw new Error('hash required');

  const receipt = await provider.getTransactionReceipt(hash);
  if (!receipt || receipt.status !== 1) {
    return ok({ verified: false, reason: 'tx not successful or not found' });
  }

  const iface = new ethers.Interface(cfg.abi);
  let invested = null;
  for (const log of receipt.logs) {
    if (log.address.toLowerCase() !== cfg.vaultAddress.toLowerCase()) continue;
    try {
      const parsed = iface.parseLog({ topics: log.topics, data: log.data });
      if (parsed && parsed.name === 'Invested') {
        invested = {
          investmentId: parsed.args.investmentId.toString(),
          user: parsed.args.user,
          sponsor: parsed.args.sponsor,
          packageAmount: fromWei(parsed.args.packageAmount),
          slotNumber: Number(parsed.args.slotNumber),
          offchainStakeId: parsed.args.offchainStakeId.toString(),
        };
      }
    } catch (_) {}
  }

  if (!invested) {
    return ok({ verified: false, reason: 'Invested event not found', to: receipt.to });
  }

  return ok({
    verified: true,
    txHash: hash,
    blockNumber: receipt.blockNumber,
    ...invested,
  });
}

async function syncRoi(args) {
  const { wallet, vault } = await getContracts();
  if (!wallet) throw new Error('operator key missing');
  const tx = await vault.syncRoi(
    BigInt(args.investmentId),
    toWei(args.totalRoiGenerated),
    Number(args.roiDays)
  );
  const receipt = await tx.wait();
  return ok({ txHash: receipt.hash, investmentId: String(args.investmentId) });
}

async function creditAutoUpgrade(args) {
  const { wallet, vault } = await getContracts();
  if (!wallet) throw new Error('operator key missing');
  const tx = await vault.creditAutoUpgradeBalance(args.sponsor, args.from || ethers.ZeroAddress, toWei(args.amount));
  const receipt = await tx.wait();
  return ok({ txHash: receipt.hash });
}

async function autoUpgrade(args) {
  const { wallet, vault } = await getContracts();
  if (!wallet) throw new Error('operator key missing');
  const tx = await vault.autoUpgrade(args.user, BigInt(args.offchainStakeId || 0));
  const receipt = await tx.wait();
  return ok({ txHash: receipt.hash });
}

async function processWithdrawal(args) {
  const { wallet, vault } = await getContracts();
  if (!wallet) throw new Error('operator key missing');
  const tx = await vault.processWithdrawal(
    args.user,
    toWei(args.grossAmount),
    BigInt(args.offchainWithdrawalId)
  );
  const receipt = await tx.wait();
  return ok({ txHash: receipt.hash, offchainWithdrawalId: String(args.offchainWithdrawalId) });
}

async function syncIncome(args) {
  const { wallet, vault } = await getContracts();
  if (!wallet) throw new Error('operator key missing');
  const tx = await vault.syncIncome(args.user, toWei(args.amount), Number(args.incomeType || 0));
  const receipt = await tx.wait();
  return ok({ txHash: receipt.hash });
}

async function creditQualifyingDirect(args) {
  const { wallet, vault } = await getContracts();
  if (!wallet) throw new Error('operator key missing');
  const tx = await vault.creditQualifyingDirect(args.sponsor, args.direct, toWei(args.packageAmount));
  const receipt = await tx.wait();
  return ok({ txHash: receipt.hash });
}

async function recordInvestment(args) {
  const { wallet, vault } = await getContracts();
  if (!wallet) throw new Error('operator key missing');
  const tx = await vault.recordInvestment(
    args.user,
    Number(args.slotNumber),
    args.sponsor || ethers.ZeroAddress,
    BigInt(args.offchainStakeId || 0),
    Boolean(args.pullFromTreasury)
  );
  const receipt = await tx.wait();
  // parse investment id from event
  let investmentId = null;
  for (const log of receipt.logs) {
    try {
      const parsed = vault.interface.parseLog({ topics: log.topics, data: log.data });
      if (parsed && parsed.name === 'Invested') {
        investmentId = parsed.args.investmentId.toString();
      }
    } catch (_) {}
  }
  return ok({ txHash: receipt.hash, investmentId });
}

async function getMember(args) {
  const { vault } = await getContracts();
  const m = await vault.members(args.user);
  return ok({
    registered: m.registered,
    sponsor: m.sponsor,
    currentSlot: Number(m.currentSlot),
    nextSlot: Number(m.nextSlot),
    sponsorWalletTotal: fromWei(m.sponsorWalletTotal),
    autoUpgradeUsed: fromWei(m.autoUpgradeUsed),
    availableSponsorBalance: fromWei(m.availableSponsorBalance),
    withdrawableBalance: fromWei(m.withdrawableBalance),
    totalWithdrawn: fromWei(m.totalWithdrawn),
    qualifyingDirectCredits: m.qualifyingDirectCredits.toString(),
  });
}

async function getInvestment(args) {
  const { vault } = await getContracts();
  const inv = await vault.getInvestment(BigInt(args.investmentId));
  return ok({
    user: inv.user,
    packageAmount: fromWei(inv.packageAmount),
    currentSlot: Number(inv.currentSlot),
    startTime: Number(inv.startTime),
    roiDays: Number(inv.roiDays),
    totalRoiGenerated: fromWei(inv.totalRoiGenerated),
    withdrawnRoi: fromWei(inv.withdrawnRoi),
    unlockedRoi: fromWei(inv.unlockedRoi),
    sponsor: inv.sponsor,
    upgradeStatus: Number(inv.upgradeStatus),
  });
}

async function getSponsorWallet(args) {
  const { vault } = await getContracts();
  const w = await vault.getSponsorWallet(args.user);
  return ok({
    total: fromWei(w.total),
    autoUpgradeUsed: fromWei(w.autoUpgradeUsed),
    available: fromWei(w.available),
  });
}

const COMMANDS = {
  verifyInvest,
  syncRoi,
  creditAutoUpgrade,
  autoUpgrade,
  processWithdrawal,
  syncIncome,
  creditQualifyingDirect,
  recordInvestment,
  getMember,
  getInvestment,
  getSponsorWallet,
};

async function main() {
  const command = process.argv[2];
  const raw = process.argv[3] || '{}';
  if (!command || !COMMANDS[command]) {
    fail('Unknown command. Available: ' + Object.keys(COMMANDS).join(', '));
    return;
  }
  let args;
  try {
    args = JSON.parse(raw);
  } catch (e) {
    fail('Invalid JSON args: ' + e.message);
    return;
  }
  try {
    await COMMANDS[command](args);
  } catch (e) {
    fail(e.message || String(e));
  }
}

main();
