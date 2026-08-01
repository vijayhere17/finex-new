const fs = require('fs');
const path = require('path');
const hre = require('hardhat');

async function main() {
  const [deployer] = await hre.ethers.getSigners();
  const network = hre.network.name;
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

  fs.writeFileSync(path.join(outDir, 'FinexVault.json'), JSON.stringify({
    address: vaultAddress,
    abi: artifact.abi,
    network,
    chainId: (await hre.ethers.provider.getNetwork()).chainId.toString(),
    usdt: usdtAddress,
    operator,
    deployedAt: new Date().toISOString(),
    deployer: deployer.address,
  }, null, 2));

  if (usdtArtifact) {
    fs.writeFileSync(path.join(outDir, 'MockUSDT.json'), JSON.stringify({
      address: usdtAddress,
      abi: usdtArtifact.abi,
      network,
    }, null, 2));
  }

  // Mirror into Laravel config-friendly file
  const laravelOut = path.join(__dirname, '..', '..', 'storage', 'app', 'blockchain');
  fs.mkdirSync(laravelOut, { recursive: true });
  fs.copyFileSync(
    path.join(outDir, 'FinexVault.json'),
    path.join(laravelOut, 'FinexVault.json')
  );

  console.log('Wrote ABI to', outDir);
}

main().catch((e) => {
  console.error(e);
  process.exit(1);
});
