require('@nomicfoundation/hardhat-toolbox');
require('dotenv').config({ path: require('path').resolve(__dirname, '../.env') });

const PRIVATE_KEY = process.env.BLOCKCHAIN_OPERATOR_KEY || process.env.WITHDRAWAL_PRIVATE_KEY || '';
const accounts = PRIVATE_KEY && PRIVATE_KEY.length >= 64 ? [PRIVATE_KEY.startsWith('0x') ? PRIVATE_KEY : `0x${PRIVATE_KEY}`] : [];

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
