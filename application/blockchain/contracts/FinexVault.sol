// SPDX-License-Identifier: MIT
pragma solidity ^0.8.20;

import "@openzeppelin/contracts/token/ERC20/IERC20.sol";
import "@openzeppelin/contracts/token/ERC20/utils/SafeERC20.sol";
import "@openzeppelin/contracts/access/AccessControl.sol";
import "@openzeppelin/contracts/utils/ReentrancyGuard.sol";
import "@openzeppelin/contracts/utils/Pausable.sol";

/**
 * @title FinexVault
 * @notice On-chain custody for Finex slot investments on BSC.
 *         Laravel remains the management layer (ROI cron, genealogy, reports).
 *         This vault holds USDT, records investments/slots/sponsor wallet,
 *         validates ROI unlock + auto-upgrade + withdrawals.
 *
 * Business rules mirrored from Finex plan:
 * - Daily ROI generated off-chain; operator syncs totals on-chain
 * - Withdrawable ROI = min(roiGenerated, qualifyingDirects * packageAmount)
 * - Qualifying direct = same-or-greater package referral credited to sponsor
 * - Sponsor wallet: total business / auto-upgrade used / available
 * - Auto-upgrade purchases next slot from available sponsor balance (no manual buy)
 * - Withdrawals transfer USDT from vault to user wallet after dual validation
 */
contract FinexVault is AccessControl, ReentrancyGuard, Pausable {
    using SafeERC20 for IERC20;

    bytes32 public constant OPERATOR_ROLE = keccak256("OPERATOR_ROLE");

    IERC20 public immutable usdt;
    uint256 public constant MAX_ROI_DAYS = 300;
    uint256 public constant WITHDRAWAL_FEE_BPS = 1000; // 10%
    uint256 public constant BPS_DENOM = 10000;
    uint8 public constant MAX_SLOT = 12;

    /// @dev Fixed Finex slot ladder (USD * 1e18)
    uint256[12] public slotAmounts;

    struct Investment {
        address user;
        uint256 packageAmount;
        uint8 currentSlot;
        uint64 startTime;
        uint32 roiDays;
        uint256 totalRoiGenerated;
        uint256 withdrawnRoi;
        uint256 unlockedRoi;
        address sponsor;
        uint8 upgradeStatus; // 0=active, 1=completed
        bool exists;
    }

    struct Member {
        bool registered;
        address sponsor;
        uint8 currentSlot;
        uint8 nextSlot;
        uint256 sponsorWalletTotal;
        uint256 autoUpgradeUsed;
        uint256 availableSponsorBalance;
        uint256 withdrawableBalance; // unlocked incomes available to withdraw
        uint256 totalWithdrawn;
        uint256 qualifyingDirectCredits; // count of same-or-greater package directs (global)
    }

    uint256 public nextInvestmentId = 1;
    mapping(uint256 => Investment) public investments;
    mapping(address => uint256[]) public memberInvestmentIds;
    mapping(address => Member) public members;

    /// @dev Per investment: number of qualifying same-or-greater package directs
    mapping(uint256 => uint256) public investmentQualifyingDirects;

    /// @dev Laravel withdrawal id => processed
    mapping(uint256 => bool) public processedWithdrawals;

    /// @dev Prevent double-credit of a downline package toward a sponsor investment
    mapping(uint256 => mapping(address => bool)) public directCreditedForInvestment;

    event MemberRegistered(address indexed user, address indexed sponsor);
    event Invested(
        uint256 indexed investmentId,
        address indexed user,
        address indexed sponsor,
        uint256 packageAmount,
        uint8 slotNumber,
        uint256 offchainStakeId
    );
    event RoiSynced(
        uint256 indexed investmentId,
        uint256 totalRoiGenerated,
        uint256 unlockedRoi,
        uint32 roiDays
    );
    event QualifyingDirectCredited(
        uint256 indexed investmentId,
        address indexed sponsor,
        address indexed direct,
        uint256 packageAmount,
        uint256 qualifyingCount
    );
    event SponsorWalletCredited(
        address indexed sponsor,
        address indexed from,
        uint256 amount,
        uint256 total,
        uint256 available
    );
    event AutoUpgraded(
        address indexed user,
        uint8 newSlot,
        uint256 price,
        uint256 availableAfter,
        uint256 offchainStakeId
    );
    event WithdrawalProcessed(
        uint256 indexed offchainWithdrawalId,
        address indexed user,
        uint256 grossAmount,
        uint256 feeAmount,
        uint256 netAmount
    );
    event IncomeSynced(address indexed user, uint256 amount, uint8 incomeType);
    event MemberProgressSynced(
        address indexed user,
        address indexed sponsor,
        uint8 currentSlot,
        uint8 nextSlot
    );

    constructor(address usdtToken, address admin, address operator) {
        require(usdtToken != address(0), "FinexVault: usdt");
        require(admin != address(0), "FinexVault: admin");
        usdt = IERC20(usdtToken);

        _grantRole(DEFAULT_ADMIN_ROLE, admin);
        _grantRole(OPERATOR_ROLE, admin);
        if (operator != address(0)) {
            _grantRole(OPERATOR_ROLE, operator);
        }

        // Finex fixed slots
        slotAmounts[0] = 10 * 1e18;
        slotAmounts[1] = 20 * 1e18;
        slotAmounts[2] = 40 * 1e18;
        slotAmounts[3] = 80 * 1e18;
        slotAmounts[4] = 160 * 1e18;
        slotAmounts[5] = 320 * 1e18;
        slotAmounts[6] = 640 * 1e18;
        slotAmounts[7] = 1280 * 1e18;
        slotAmounts[8] = 2560 * 1e18;
        slotAmounts[9] = 5120 * 1e18;
        slotAmounts[10] = 10240 * 1e18;
        slotAmounts[11] = 20480 * 1e18;
    }

    // -------------------------------------------------------------------------
    // Views
    // -------------------------------------------------------------------------

    function getSlotAmount(uint8 slotNumber) public view returns (uint256) {
        require(slotNumber >= 1 && slotNumber <= MAX_SLOT, "FinexVault: slot");
        return slotAmounts[slotNumber - 1];
    }

    function requiredReferrals(uint256 roiGenerated, uint256 packageAmount) public pure returns (uint256) {
        if (packageAmount == 0 || roiGenerated == 0) return 0;
        return roiGenerated / packageAmount;
    }

    function computeUnlockedRoi(
        uint256 roiGenerated,
        uint256 packageAmount,
        uint256 qualifyingDirects
    ) public pure returns (uint256) {
        if (roiGenerated == 0 || packageAmount == 0) return 0;
        uint256 cap = qualifyingDirects * packageAmount;
        return roiGenerated < cap ? roiGenerated : cap;
    }

    function memberInvestments(address user) external view returns (uint256[] memory) {
        return memberInvestmentIds[user];
    }

    function getSponsorWallet(address user)
        external
        view
        returns (uint256 total, uint256 autoUpgradeUsed, uint256 available)
    {
        Member storage m = members[user];
        return (m.sponsorWalletTotal, m.autoUpgradeUsed, m.availableSponsorBalance);
    }

    function getInvestment(uint256 investmentId)
        external
        view
        returns (
            address user,
            uint256 packageAmount,
            uint8 currentSlot,
            uint64 startTime,
            uint32 roiDays,
            uint256 totalRoiGenerated,
            uint256 withdrawnRoi,
            uint256 unlockedRoi,
            address sponsor,
            uint8 upgradeStatus
        )
    {
        Investment storage inv = investments[investmentId];
        require(inv.exists, "FinexVault: missing");
        return (
            inv.user,
            inv.packageAmount,
            inv.currentSlot,
            inv.startTime,
            inv.roiDays,
            inv.totalRoiGenerated,
            inv.withdrawnRoi,
            inv.unlockedRoi,
            inv.sponsor,
            inv.upgradeStatus
        );
    }

    // -------------------------------------------------------------------------
    // Registration / Investment (user-funded)
    // -------------------------------------------------------------------------

    function register(address sponsor) external whenNotPaused {
        _ensureMember(msg.sender, sponsor);
    }

    /**
     * @notice Backfill on-chain slot progress for members who already activated
     *         slots off-chain (legacy admin approval) so the next invest() matches.
     *         Does not move USDT and does not create Investment rows.
     */
    function syncMemberProgress(address user, address sponsor, uint8 currentSlot)
        external
        onlyRole(OPERATOR_ROLE)
        whenNotPaused
    {
        require(user != address(0), "FinexVault: user");
        require(currentSlot <= MAX_SLOT, "FinexVault: slot");

        _ensureMember(user, sponsor);
        Member storage m = members[user];

        // Only advance (or set) progress — never rewind.
        require(currentSlot >= m.currentSlot, "FinexVault: rewind");

        if (sponsor != address(0) && m.sponsor == address(0)) {
            m.sponsor = sponsor;
        }

        m.currentSlot = currentSlot;
        m.nextSlot = currentSlot == 0 ? 1 : (currentSlot < MAX_SLOT ? currentSlot + 1 : 0);

        emit MemberProgressSynced(user, m.sponsor, m.currentSlot, m.nextSlot);
    }

    function _ensureMember(address user, address sponsor) internal {
        Member storage m = members[user];
        if (m.registered) {
            return;
        }
        if (sponsor != address(0)) {
            require(sponsor != user, "FinexVault: self");
            // Shell-register sponsor so genealogy + wallet credits work on first invest.
            if (!members[sponsor].registered) {
                members[sponsor].registered = true;
                members[sponsor].nextSlot = 1;
                emit MemberRegistered(sponsor, address(0));
            }
        }
        m.registered = true;
        m.sponsor = sponsor;
        m.nextSlot = 1;
        emit MemberRegistered(user, sponsor);
    }

    /**
     * @notice Invest / activate a Finex slot. USDT is pulled into this vault.
     * @param slotNumber Sequential slot 1..12
     * @param sponsor Sponsor wallet (used if member not yet registered)
     * @param offchainStakeId Laravel staked_users / request id for sync
     */
    function invest(uint8 slotNumber, address sponsor, uint256 offchainStakeId)
        external
        nonReentrant
        whenNotPaused
    {
        require(slotNumber >= 1 && slotNumber <= MAX_SLOT, "FinexVault: slot");
        uint256 packageAmount = getSlotAmount(slotNumber);

        _ensureMember(msg.sender, sponsor);
        Member storage m = members[msg.sender];

        require(m.nextSlot == slotNumber, "FinexVault: sequence");

        usdt.safeTransferFrom(msg.sender, address(this), packageAmount);

        uint256 investmentId = nextInvestmentId++;
        investments[investmentId] = Investment({
            user: msg.sender,
            packageAmount: packageAmount,
            currentSlot: slotNumber,
            startTime: uint64(block.timestamp),
            roiDays: 0,
            totalRoiGenerated: 0,
            withdrawnRoi: 0,
            unlockedRoi: 0,
            sponsor: m.sponsor,
            upgradeStatus: 0,
            exists: true
        });
        memberInvestmentIds[msg.sender].push(investmentId);

        m.currentSlot = slotNumber;
        m.nextSlot = slotNumber < MAX_SLOT ? slotNumber + 1 : 0;

        // Credit sponsor wallet (total business) with full package; available credits
        // for auto-upgrade are applied by operator per compensation config (2nd/3rd %).
        if (m.sponsor != address(0)) {
            _ensureMember(m.sponsor, address(0));
            Member storage s = members[m.sponsor];
            s.sponsorWalletTotal += packageAmount;
            emit SponsorWalletCredited(
                m.sponsor,
                msg.sender,
                packageAmount,
                s.sponsorWalletTotal,
                s.availableSponsorBalance
            );

            _creditQualifyingDirects(m.sponsor, msg.sender, packageAmount);
        }

        emit Invested(investmentId, msg.sender, m.sponsor, packageAmount, slotNumber, offchainStakeId);
    }

    /**
     * @notice Operator-recorded investment for sync when user paid via approved Laravel flow
     *         (e.g. after verifying an invest() tx, or admin-approved test activation that
     *         already moved funds). Does NOT pull USDT — funds must already be in vault
     *         or pre-funded by treasury for testnet demos.
     */
    function recordInvestment(
        address user,
        uint8 slotNumber,
        address sponsor,
        uint256 offchainStakeId,
        bool pullFromTreasury
    ) external onlyRole(OPERATOR_ROLE) nonReentrant whenNotPaused returns (uint256 investmentId) {
        require(user != address(0), "FinexVault: user");
        require(slotNumber >= 1 && slotNumber <= MAX_SLOT, "FinexVault: slot");
        uint256 packageAmount = getSlotAmount(slotNumber);

        Member storage m = members[user];
        if (!m.registered) {
            m.registered = true;
            m.sponsor = sponsor;
            m.nextSlot = 1;
            emit MemberRegistered(user, sponsor);
        }

        require(
            (m.currentSlot + 1 == slotNumber) || (m.currentSlot == 0 && slotNumber == 1),
            "FinexVault: order"
        );

        if (pullFromTreasury) {
            // Optional: pull from operator (treasury) for testnet admin activations
            usdt.safeTransferFrom(msg.sender, address(this), packageAmount);
        }

        investmentId = nextInvestmentId++;
        investments[investmentId] = Investment({
            user: user,
            packageAmount: packageAmount,
            currentSlot: slotNumber,
            startTime: uint64(block.timestamp),
            roiDays: 0,
            totalRoiGenerated: 0,
            withdrawnRoi: 0,
            unlockedRoi: 0,
            sponsor: m.sponsor == address(0) ? sponsor : m.sponsor,
            upgradeStatus: 0,
            exists: true
        });
        memberInvestmentIds[user].push(investmentId);

        m.currentSlot = slotNumber;
        m.nextSlot = slotNumber < MAX_SLOT ? slotNumber + 1 : 0;

        address sp = investments[investmentId].sponsor;
        if (sp != address(0)) {
            if (!members[sp].registered) {
                members[sp].registered = true;
                members[sp].nextSlot = 1;
                emit MemberRegistered(sp, address(0));
            }
            Member storage s = members[sp];
            s.sponsorWalletTotal += packageAmount;
            emit SponsorWalletCredited(sp, user, packageAmount, s.sponsorWalletTotal, s.availableSponsorBalance);
            _creditQualifyingDirects(sp, user, packageAmount);
        }

        emit Invested(investmentId, user, sp, packageAmount, slotNumber, offchainStakeId);
    }

    // -------------------------------------------------------------------------
    // Auto-upgrade credits & activation (operator / vault)
    // -------------------------------------------------------------------------

    /**
     * @notice Credit available sponsor balance (auto-upgrade pot) — Laravel computes %
     *         from 2nd/3rd direct per config/income.php and syncs here.
     */
    function creditAutoUpgradeBalance(address sponsor, address from, uint256 amount)
        external
        onlyRole(OPERATOR_ROLE)
        whenNotPaused
    {
        require(amount > 0, "FinexVault: amount");
        require(sponsor != address(0), "FinexVault: sponsor");
        _ensureMember(sponsor, address(0));
        Member storage s = members[sponsor];
        s.availableSponsorBalance += amount;
        emit SponsorWalletCredited(sponsor, from, amount, s.sponsorWalletTotal, s.availableSponsorBalance);
        _tryAutoUpgrade(sponsor, 0);
    }

    /**
     * @notice Auto-purchase next slot from available sponsor balance.
     */
    function autoUpgrade(address user, uint256 offchainStakeId)
        external
        onlyRole(OPERATOR_ROLE)
        nonReentrant
        whenNotPaused
        returns (bool)
    {
        return _tryAutoUpgrade(user, offchainStakeId);
    }

    function _tryAutoUpgrade(address user, uint256 offchainStakeId) internal returns (bool) {
        Member storage m = members[user];
        if (!m.registered || m.nextSlot == 0 || m.nextSlot > MAX_SLOT) {
            return false;
        }

        uint256 price = getSlotAmount(m.nextSlot);
        if (m.availableSponsorBalance < price) {
            return false;
        }

        m.availableSponsorBalance -= price;
        m.autoUpgradeUsed += price;

        uint8 newSlot = m.nextSlot;
        uint256 investmentId = nextInvestmentId++;
        investments[investmentId] = Investment({
            user: user,
            packageAmount: price,
            currentSlot: newSlot,
            startTime: uint64(block.timestamp),
            roiDays: 0,
            totalRoiGenerated: 0,
            withdrawnRoi: 0,
            unlockedRoi: 0,
            sponsor: m.sponsor,
            upgradeStatus: 0,
            exists: true
        });
        memberInvestmentIds[user].push(investmentId);

        m.currentSlot = newSlot;
        m.nextSlot = newSlot < MAX_SLOT ? newSlot + 1 : 0;

        emit AutoUpgraded(user, newSlot, price, m.availableSponsorBalance, offchainStakeId);
        emit Invested(investmentId, user, m.sponsor, price, newSlot, offchainStakeId);

        // Chain further upgrades if balance allows
        _tryAutoUpgrade(user, 0);
        return true;
    }

    // -------------------------------------------------------------------------
    // ROI sync + unlock
    // -------------------------------------------------------------------------

    /**
     * @notice Sync Daily ROI totals from Laravel cron. Unlocks using qualifying directs.
     */
    function syncRoi(uint256 investmentId, uint256 totalRoiGenerated, uint32 roiDays)
        external
        onlyRole(OPERATOR_ROLE)
        whenNotPaused
    {
        Investment storage inv = investments[investmentId];
        require(inv.exists, "FinexVault: missing");
        require(totalRoiGenerated >= inv.totalRoiGenerated, "FinexVault: roi rewind");
        require(roiDays <= MAX_ROI_DAYS, "FinexVault: days");

        inv.totalRoiGenerated = totalRoiGenerated;
        inv.roiDays = roiDays;

        uint256 unlocked = computeUnlockedRoi(
            inv.totalRoiGenerated,
            inv.packageAmount,
            investmentQualifyingDirects[investmentId]
        );

        if (unlocked > inv.unlockedRoi) {
            uint256 delta = unlocked - inv.unlockedRoi;
            inv.unlockedRoi = unlocked;
            members[inv.user].withdrawableBalance += delta;
        }

        if (roiDays >= MAX_ROI_DAYS) {
            inv.upgradeStatus = 1;
        }

        emit RoiSynced(investmentId, inv.totalRoiGenerated, inv.unlockedRoi, inv.roiDays);
    }

    /**
     * @notice Sync non-ROI incomes (Level ROI, Auto Upgrade Income) into withdrawable balance.
     * @param incomeType 4=Level ROI, 11=Auto Upgrade Income, 2=Daily ROI (manual adjust)
     */
    function syncIncome(address user, uint256 amount, uint8 incomeType)
        external
        onlyRole(OPERATOR_ROLE)
        whenNotPaused
    {
        require(amount > 0, "FinexVault: amount");
        require(user != address(0), "FinexVault: user");
        _ensureMember(user, address(0));
        Member storage m = members[user];
        m.withdrawableBalance += amount;
        emit IncomeSynced(user, amount, incomeType);
    }

    /**
     * @notice Credit a qualifying same-or-greater package direct against each of sponsor's
     *         open investments that this package can unlock.
     */
    function creditQualifyingDirect(address sponsor, address direct, uint256 directPackageAmount)
        external
        onlyRole(OPERATOR_ROLE)
        whenNotPaused
    {
        _creditQualifyingDirects(sponsor, direct, directPackageAmount);
    }

    function _creditQualifyingDirects(address sponsor, address direct, uint256 directPackageAmount) internal {
        if (sponsor == address(0) || direct == address(0)) return;

        Member storage s = members[sponsor];
        s.qualifyingDirectCredits += 1;

        uint256[] storage ids = memberInvestmentIds[sponsor];
        for (uint256 i = 0; i < ids.length; i++) {
            uint256 investmentId = ids[i];
            Investment storage inv = investments[investmentId];
            if (!inv.exists || inv.upgradeStatus == 1) continue;
            if (directPackageAmount < inv.packageAmount) continue;
            if (directCreditedForInvestment[investmentId][direct]) continue;

            directCreditedForInvestment[investmentId][direct] = true;
            investmentQualifyingDirects[investmentId] += 1;

            uint256 unlocked = computeUnlockedRoi(
                inv.totalRoiGenerated,
                inv.packageAmount,
                investmentQualifyingDirects[investmentId]
            );

            if (unlocked > inv.unlockedRoi) {
                uint256 delta = unlocked - inv.unlockedRoi;
                inv.unlockedRoi = unlocked;
                members[inv.user].withdrawableBalance += delta;
            }

            emit QualifyingDirectCredited(
                investmentId,
                sponsor,
                direct,
                directPackageAmount,
                investmentQualifyingDirects[investmentId]
            );
            emit RoiSynced(investmentId, inv.totalRoiGenerated, inv.unlockedRoi, inv.roiDays);
        }
    }

    // -------------------------------------------------------------------------
    // Withdrawals
    // -------------------------------------------------------------------------

    /**
     * @notice Laravel-validated withdrawal. Contract re-checks withdrawable balance
     *         and transfers USDT directly to the user wallet (minus 10% fee).
     */
    function processWithdrawal(
        address user,
        uint256 grossAmount,
        uint256 offchainWithdrawalId
    ) external onlyRole(OPERATOR_ROLE) nonReentrant whenNotPaused {
        require(user != address(0), "FinexVault: user");
        require(grossAmount > 0, "FinexVault: amount");
        require(!processedWithdrawals[offchainWithdrawalId], "FinexVault: dup");

        Member storage m = members[user];
        require(m.registered, "FinexVault: registered");
        require(m.withdrawableBalance >= grossAmount, "FinexVault: balance");

        processedWithdrawals[offchainWithdrawalId] = true;
        m.withdrawableBalance -= grossAmount;
        m.totalWithdrawn += grossAmount;

        uint256 feeAmount = (grossAmount * WITHDRAWAL_FEE_BPS) / BPS_DENOM;
        uint256 netAmount = grossAmount - feeAmount;

        // Attribute ROI withdrawn proportionally across investments
        _attributeRoiWithdrawal(user, grossAmount);

        usdt.safeTransfer(user, netAmount);
        // Fee remains in vault (company processing fee)

        emit WithdrawalProcessed(offchainWithdrawalId, user, grossAmount, feeAmount, netAmount);
    }

    function _attributeRoiWithdrawal(address user, uint256 amount) internal {
        uint256 remaining = amount;
        uint256[] storage ids = memberInvestmentIds[user];
        for (uint256 i = 0; i < ids.length && remaining > 0; i++) {
            Investment storage inv = investments[ids[i]];
            if (!inv.exists) continue;
            uint256 open = inv.unlockedRoi > inv.withdrawnRoi ? inv.unlockedRoi - inv.withdrawnRoi : 0;
            if (open == 0) continue;
            uint256 take = open < remaining ? open : remaining;
            inv.withdrawnRoi += take;
            remaining -= take;
        }
    }

    // -------------------------------------------------------------------------
    // Admin
    // -------------------------------------------------------------------------

    function pause() external onlyRole(DEFAULT_ADMIN_ROLE) {
        _pause();
    }

    function unpause() external onlyRole(DEFAULT_ADMIN_ROLE) {
        _unpause();
    }

    function rescueToken(address token, address to, uint256 amount) external onlyRole(DEFAULT_ADMIN_ROLE) {
        require(to != address(0), "FinexVault: to");
        IERC20(token).safeTransfer(to, amount);
    }

    function setSlotAmount(uint8 slotNumber, uint256 amount) external onlyRole(DEFAULT_ADMIN_ROLE) {
        require(slotNumber >= 1 && slotNumber <= MAX_SLOT, "FinexVault: slot");
        slotAmounts[slotNumber - 1] = amount;
    }
}
