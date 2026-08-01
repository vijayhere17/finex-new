// SPDX-License-Identifier: MIT
pragma solidity ^0.8.20;

import "@openzeppelin/contracts/token/ERC20/ERC20.sol";
import "@openzeppelin/contracts/access/Ownable.sol";

/**
 * @title MockUSDT
 * @notice BSC Testnet stand-in for USDT (18 decimals, matching mainnet BEP20 USDT).
 */
contract MockUSDT is ERC20, Ownable {
    constructor() ERC20("Tether USD", "USDT") Ownable(msg.sender) {
        _mint(msg.sender, 1_000_000_000 * 10 ** 18);
    }

    function decimals() public pure override returns (uint8) {
        return 18;
    }

    function mint(address to, uint256 amount) external onlyOwner {
        _mint(to, amount);
    }

    function faucet(uint256 amount) external {
        require(amount <= 10_000 * 10 ** 18, "MockUSDT: max 10000");
        _mint(msg.sender, amount);
    }
}
