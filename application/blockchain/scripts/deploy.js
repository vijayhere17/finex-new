const fs = require('fs');
const path = require('path');
const hre = require('hardhat');

async function main() {
  const [deployer] = await hre.ethers.getSigners();
  const network = hre.network.name;

  if (!deployer) {
    throw new Error(
      'No deployer account for network "' + network + '".\n' +
      'Set BLOCKCHAIN_OPERATOR_KEY in application/.env or blockchain/.env\n' +
      '  BLOCKCHAIN_OPERATOR_KEY=0xYOUR_64_CHAR_PRIVATE_KEY\n' +
      'Or in PowerShell for this session:\n' +
      '  $env:BLOCKCHAIN_OPERATOR_KEY = "0xYOUR_PRIVATE_KEY"\n' +
      'The wallet must have BSC testnet BNB for gas.'
    );
  }

  console.log('Deploying Finex vault with', deployer.address, 'on', network);

  const balance = await hre.ethers.provider.getBalance(deployer.address);
  console.log('Deployer balance:', hre.ethers.formatEther(balance), 'BNB');

  let usdtAddress = process.env.USDT_CONTRACT || '';
  let mock = null;

  if (!usdtAddress || network === 'hardhat' || network === 'localhost' || process.env.DEPLOY_MOCK_USDT === '1') {
    const MockUSDT = await hre.ethers.getContractFactory('MockUSDT');
    mock = await MockUSDT.deploy();
    await mock.waitForDeployment();
    usdtAddress = await mock.getAddress();
    console.log('MockUSDT:', usdtAddress);
  } else {
    console.log('Using existing USDT:', usdtAddress);
  }

  const operator = process.env.BLOCKCHAIN_OPERATOR_ADDRESS || deployer.address;

  const FinexVault = await hre.ethers.getContractFactory('FinexVault');
  const vault = await FinexVault.deploy(usdtAddress, deployer.address, operator);
  await vault.waitForDeployment();
  const vaultAddress = await vault.getAddress();
  console.log('FinexVault:', vaultAddress);
  console.log('Operator:', operator);

  // Export ABI + addresses for Laravel
  const artifact = await hre.artifacts.readArtifact('FinexVault');
  const usdtArtifact = mock
    ? await hre.artifacts.readArtifact('MockUSDT')
    : null;

  const outDir = path.join(__dirname, '..', 'abi');
  fs.mkdirSync(outDir, { recursive: true });

  const deployed = {
    address: vaultAddress,
    abi: artifact.abi,
    network,
    chainId: (await hre.ethers.provider.getNetwork()).chainId.toString(),
    usdt: usdtAddress,
    operator,
    deployedAt: new Date().toISOString(),
    deployer: deployer.address,
  };

  // Always write a network-specific deploy file (safe to keep local; gitignored pattern)
  const deployedName = `deployed.${network}.json`;
  fs.writeFileSync(path.join(outDir, deployedName), JSON.stringify(deployed, null, 2));

  // Keep FinexVault.json ABI in sync, but for non-local networks do not force-commit addresses.
  // Local hardhat deploys still write full file for tests.
  if (network === 'hardhat' || network === 'localhost') {
    fs.writeFileSync(path.join(outDir, 'FinexVault.json'), JSON.stringify({
      ...deployed,
      address: '',
      usdt: '',
      operator: '',
      note: 'ABI only in repo. Set FINEX_VAULT_ADDRESS / BLOCKCHAIN_USDT_ADDRESS in .env after testnet deploy.',
    }, null, 2));
  } else {
    // Update local ABI file addresses for this machine (user from .env in Laravel)
    fs.writeFileSync(path.join(outDir, 'FinexVault.json'), JSON.stringify(deployed, null, 2));
  }

  if (usdtArtifact) {
    fs.writeFileSync(path.join(outDir, 'MockUSDT.json'), JSON.stringify({
      address: network === 'hardhat' || network === 'localhost' ? '' : usdtAddress,
      abi: usdtArtifact.abi,
      network,
    }, null, 2));
  }

  // Mirror into Laravel storage (preferred runtime source alongside .env)
  const laravelOut = path.join(__dirname, '..', '..', 'storage', 'app', 'blockchain');
  fs.mkdirSync(laravelOut, { recursive: true });
  fs.writeFileSync(path.join(laravelOut, 'FinexVault.json'), JSON.stringify(deployed, null, 2));
  fs.writeFileSync(path.join(laravelOut, deployedName), JSON.stringify(deployed, null, 2));

  console.log('Wrote ABI to', outDir);
  console.log('Wrote Laravel copy to', laravelOut);
  console.log('\nAdd these to application/.env :');
  console.log('FINEX_VAULT_ADDRESS=' + vaultAddress);
  console.log('BLOCKCHAIN_USDT_ADDRESS=' + usdtAddress);
  console.log('BLOCKCHAIN_OPERATOR_ADDRESS=' + operator);
  console.log('BSC_CHAIN_ID=' + deployed.chainId);
}

main().catch((e) => {
  console.error(e);
  process.exit(1);
});
