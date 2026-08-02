<?php

/**
 * Finex on-chain vault configuration (BSC Testnet by default).
 * Laravel remains the management layer; FinexVault holds USDT.
 */

$readJson = static function (string $path): array {
    try {
        if (!is_file($path)) {
            return [];
        }
        $decoded = json_decode((string) file_get_contents($path), true);
        return is_array($decoded) ? $decoded : [];
    } catch (\Throwable $e) {
        return [];
    }
};

$deployment = [];
$abiPayload = [];

try {
    $deployment = $readJson(storage_path('app/blockchain/FinexVault.json'));
    $abiPayload = $readJson(base_path('blockchain/abi/FinexVault.json'));
    if ($abiPayload === []) {
        $abiPayload = $deployment;
    }
} catch (\Throwable $e) {
    $deployment = [];
    $abiPayload = [];
}

$abiPath = base_path('blockchain/abi/FinexVault.json');
try {
    $storageAbi = storage_path('app/blockchain/FinexVault.json');
    if (is_file($storageAbi)) {
        $abiPath = $storageAbi;
    }
} catch (\Throwable $e) {
    // keep default
}

return [

    'enabled' => (bool) env('BLOCKCHAIN_ENABLED', true),

    // 97 = BSC Testnet, 56 = BSC Mainnet
    'chain_id' => (int) env('BSC_CHAIN_ID', 97),

    'network' => env('BSC_NETWORK', 'bscTestnet'),

    'rpc_url' => env('BSC_TESTNET_RPC', env('BSC_RPC_URL', 'https://data-seed-prebsc-1-s1.binance.org:8545')),

    'explorer_tx_url' => env('BSC_EXPLORER_TX_URL', 'https://testnet.bscscan.com/tx/'),

    // FinexVault address (set after deploy, or via abi/FinexVault.json)
    'vault_address' => env('FINEX_VAULT_ADDRESS', $abiPayload['address'] ?? ($deployment['address'] ?? '')),

    // USDT used by the vault (MockUSDT on testnet, real USDT on mainnet)
    'usdt_address' => env(
        'BLOCKCHAIN_USDT_ADDRESS',
        $abiPayload['usdt'] ?? ($deployment['usdt'] ?? env('USDT_CONTRACT', '0x55d398326f99059fF775485246999027B3197955'))
    ),

    // Operator signs syncRoi / processWithdrawal / creditAutoUpgradeBalance
    'operator_address' => env('BLOCKCHAIN_OPERATOR_ADDRESS', $abiPayload['operator'] ?? ''),
    'operator_key' => env('BLOCKCHAIN_OPERATOR_KEY', env('WITHDRAWAL_PRIVATE_KEY', '')),

    // Optional absolute node binary. Prefer forward slashes on Windows.
    'node_binary' => env('NODE_BINARY', ''),

    // Node helper used by Laravel (Symfony Process — Windows-safe)
    'node_script' => base_path('blockchain/scripts/operator-cli.js'),

    'abi_path' => $abiPath,

    // Slot activation requires a verified FinexVault.invest() tx (no admin pending queue).
    'require_onchain_invest' => (bool) env('BLOCKCHAIN_REQUIRE_ONCHAIN_INVEST', true),

    // When true, withdrawals are paid from FinexVault instead of external send-edu.php
    'withdrawals_via_vault' => (bool) env('BLOCKCHAIN_WITHDRAWALS_VIA_VAULT', true),

    // Sync ROI unlock + auto-upgrade credits to the vault after off-chain processing
    'sync_roi_onchain' => (bool) env('BLOCKCHAIN_SYNC_ROI', true),
    'sync_auto_upgrade_onchain' => (bool) env('BLOCKCHAIN_SYNC_AUTO_UPGRADE', true),

    'usdt_decimals' => 18,
];
