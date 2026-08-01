const { expect } = require('chai');
const { ethers } = require('hardhat');

describe('FinexVault', function () {
  async function deployFixture() {
    const [admin, operator, alice, bob, carol] = await ethers.getSigners();

    const MockUSDT = await ethers.getContractFactory('MockUSDT');
    const usdt = await MockUSDT.deploy();
    await usdt.waitForDeployment();

    const FinexVault = await ethers.getContractFactory('FinexVault');
    const vault = await FinexVault.deploy(
      await usdt.getAddress(),
      admin.address,
      operator.address
    );
    await vault.waitForDeployment();

    // Fund users
    for (const u of [alice, bob, carol, operator]) {
      await usdt.transfer(u.address, ethers.parseEther('100000'));
    }

    return { usdt, vault, admin, operator, alice, bob, carol };
  }

  it('computes ROI unlock from qualifying referrals', async function () {
    const { vault } = await deployFixture();
    const pkg = ethers.parseEther('100');
    const roi = ethers.parseEther('260');
    expect(await vault.requiredReferrals(roi, pkg)).to.equal(2n);
    expect(await vault.computeUnlockedRoi(roi, pkg, 1n)).to.equal(ethers.parseEther('100'));
    expect(await vault.computeUnlockedRoi(roi, pkg, 2n)).to.equal(ethers.parseEther('200'));
    expect(await vault.computeUnlockedRoi(roi, pkg, 3n)).to.equal(roi);
  });

  it('accepts sequential slot investment and credits sponsor wallet total', async function () {
    const { usdt, vault, alice, bob } = await deployFixture();
    const vaultAddr = await vault.getAddress();
    const slot1 = ethers.parseEther('10');

    await vault.connect(alice).register(ethers.ZeroAddress);
    await usdt.connect(bob).approve(vaultAddr, slot1);
    await vault.connect(bob).invest(1, alice.address, 101);

    const [total, used, available] = await vault.getSponsorWallet(alice.address);
    expect(total).to.equal(slot1);
    expect(used).to.equal(0n);
    expect(available).to.equal(0n);

    const inv = await vault.getInvestment(1);
    expect(inv.user).to.equal(bob.address);
    expect(inv.packageAmount).to.equal(slot1);
    expect(inv.sponsor).to.equal(alice.address);
  });

  it('auto-upgrades when available sponsor balance covers next slot', async function () {
    const { usdt, vault, operator, alice } = await deployFixture();
    const vaultAddr = await vault.getAddress();

    await vault.connect(alice).register(ethers.ZeroAddress);
    await usdt.connect(alice).approve(vaultAddr, ethers.parseEther('10'));
    await vault.connect(alice).invest(1, ethers.ZeroAddress, 1);

    // Credit auto-upgrade pot with enough for slot 2 ($20)
    await vault.connect(operator).creditAutoUpgradeBalance(
      alice.address,
      alice.address,
      ethers.parseEther('20')
    );

    const member = await vault.members(alice.address);
    expect(member.currentSlot).to.equal(2);
    expect(member.autoUpgradeUsed).to.equal(ethers.parseEther('20'));
    expect(member.availableSponsorBalance).to.equal(0n);
  });

  it('syncs ROI and unlocks after qualifying directs; processes withdrawal with 10% fee', async function () {
    const { usdt, vault, operator, alice, bob, carol } = await deployFixture();
    const vaultAddr = await vault.getAddress();

    await vault.connect(alice).register(ethers.ZeroAddress);
    await usdt.connect(alice).approve(vaultAddr, ethers.parseEther('10'));
    await vault.connect(alice).invest(1, ethers.ZeroAddress, 1);

    // Fund vault extras for withdrawal liquidity beyond alice's own stake? Alice's $10 is in vault.
    // Sync ROI $10 then unlock with 1 qualifying direct.
    await vault.connect(operator).syncRoi(1, ethers.parseEther('10'), 100);
    let inv = await vault.getInvestment(1);
    expect(inv.unlockedRoi).to.equal(0n);

    // Bob invests same-or-greater package under alice → unlocks $10
    await usdt.connect(bob).approve(vaultAddr, ethers.parseEther('10'));
    await vault.connect(bob).invest(1, alice.address, 2);

    inv = await vault.getInvestment(1);
    expect(inv.unlockedRoi).to.equal(ethers.parseEther('10'));

    // Top up vault so withdrawal of unlocked ROI is payable (ROI is accounting, not extra deposit)
    await usdt.connect(operator).transfer(vaultAddr, ethers.parseEther('100'));

    const before = await usdt.balanceOf(alice.address);
    await vault.connect(operator).processWithdrawal(alice.address, ethers.parseEther('10'), 55);
    const after = await usdt.balanceOf(alice.address);

    // net = 90% of 10
    expect(after - before).to.equal(ethers.parseEther('9'));
    expect(await vault.processedWithdrawals(55)).to.equal(true);

    // Carol larger package also qualifies
    await usdt.connect(carol).approve(vaultAddr, ethers.parseEther('20'));
    await vault.connect(carol).invest(1, alice.address, 3);
  });

  it('rejects out-of-sequence slot purchase', async function () {
    const { usdt, vault, bob } = await deployFixture();
    const vaultAddr = await vault.getAddress();
    await usdt.connect(bob).approve(vaultAddr, ethers.parseEther('20'));
    await expect(vault.connect(bob).invest(2, ethers.ZeroAddress, 1)).to.be.revertedWith(
      'FinexVault: sequence'
    );
  });
});
