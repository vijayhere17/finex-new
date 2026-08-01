require('@nomicfoundation/hardhat-toolbox');
const path = require('path');
const dotenv = require('dotenv');

// Load application/.env then blockchain/.env (later file wins for overlapping keys)
dotenv.config({ path: path.resolve(__dirname, '../.env') });
dotenv.config({ path: path.resolve(__dirname, '.env'), override: true });

function resolveAccounts() {
  let key = process.env.BLOCKCHAIN_OPERATOR_KEY || process.env.WITHDRAWAL_PRIVATE_KEY || '';
  key = String(key).trim().replace(/^["']|["']$/g, '');
  if (!key) return [];
  if (!key.startsWith('0x')) key = `0x${key}`;
  // 0x + 64 hex chars
  if (!/^0x[0-9a-fA-F]{64}$/.test(key)) {
    console.warn('Warning: BLOCKCHAIN_OPERATOR_KEY / WITHDRAWAL_PRIVATE_KEY is set but invalid (need 64 hex chars).');
    return [];
  }
  return [key];
}

const accounts = resolveAccounts();

/** @type import('hardhat/config').HardhatUserConfig */
module.exports = {
  solidity: {
    version: '0.8.20',
    settings: {
      optimizer: { enabled: true, runs: 200 },
    },
  },
  paths: {
    sources: './contracts',
    tests: './test',
    cache: './cache',
    artifacts: './artifacts',
  },
  networks: {
    hardhat: {},
    bscTestnet: {
      url: process.env.BSC_TESTNET_RPC || 'https://data-seed-prebsc-1-s1.binance.org:8545',
      chainId: 97,
      accounts,
    },
    bscMainnet: {
      url: process.env.BSC_MAINNET_RPC || 'https://bsc-dataseed1.binance.org/',
      chainId: 56,
      accounts,
    },
  },
};
