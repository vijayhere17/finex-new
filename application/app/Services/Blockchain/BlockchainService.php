<?php

namespace App\Services\Blockchain;

use App\Models\BlockchainTransaction;
use App\Models\User;
use App\Models\UserStaked;
use Illuminate\Support\Facades\Log;

/**
 * Bridge between Laravel Finex management layer and FinexVault (BSC).
 * Uses Node operator-cli.js (ethers) — same shell pattern as existing txn verify.
 */
class BlockchainService
{
    public function enabled(): bool
    {
        return (bool) config('blockchain.enabled', false)
            && !empty(config('blockchain.vault_address'));
    }

    public function vaultAddress(): string
    {
        return (string) config('blockchain.vault_address', '');
    }

    public function usdtAddress(): string
    {
        return (string) config('blockchain.usdt_address', '');
    }

    public function chainId(): int
    {
        return (int) config('blockchain.chain_id', 97);
    }

    public function vaultAbiJson(): string
    {
        $path = config('blockchain.abi_path');
        if (!$path || !is_file($path)) {
            return '[]';
        }
        $payload = json_decode((string) file_get_contents($path), true);
        return json_encode($payload['abi'] ?? []);
    }

    public function normalizeWallet(?string $username): string
    {
        if ($username === null || $username === '') {
            return '';
        }
        $parts = explode('-', $username);
        return strtolower(trim($parts[0]));
    }

    /**
     * Run operator-cli.js command and decode JSON response.
     */
    public function call(string $command, array $args = []): array
    {
        $script = config('blockchain.node_script');
        if (!$script || !is_file($script)) {
            return ['success' => false, 'error' => 'operator-cli.js missing'];
        }

        $env = [
            'BSC_RPC_URL' => config('blockchain.rpc_url'),
            'FINEX_VAULT_ADDRESS' => config('blockchain.vault_address'),
            'BLOCKCHAIN_USDT_ADDRESS' => config('blockchain.usdt_address'),
            'BLOCKCHAIN_OPERATOR_KEY' => config('blockchain.operator_key'),
            'FINEX_ABI_PATH' => config('blockchain.abi_path'),
            'PATH' => getenv('PATH') ?: '/usr/bin:/bin',
        ];

        $envPrefix = '';
        foreach ($env as $k => $v) {
            if ($v === null || $v === '') {
                continue;
            }
            $envPrefix .= $k.'='.escapeshellarg((string) $v).' ';
        }

        $json = json_encode($args, JSON_UNESCAPED_SLASHES);
        $cmd = $envPrefix.'node '.escapeshellarg($script).' '.escapeshellarg($command).' '.escapeshellarg($json).' 2>&1';

        $output = shell_exec($cmd);
        $decoded = json_decode((string) $output, true);

        if (!is_array($decoded)) {
            Log::warning('BlockchainService invalid response', ['cmd' => $command, 'out' => $output]);
            return ['success' => false, 'error' => 'Invalid operator response', 'raw' => $output];
        }

        return $decoded;
    }

    protected function logTx(array $data): BlockchainTransaction
    {
        return BlockchainTransaction::create(array_merge([
            'status' => 'pending',
        ], $data));
    }

    public function verifyInvestTransaction(string $hash): array
    {
        return $this->call('verifyInvest', ['hash' => $hash]);
    }

    public function syncRoi(UserStaked $stake): array
    {
        if (!$this->enabled() || !config('blockchain.sync_roi_onchain')) {
            return ['success' => false, 'error' => 'disabled'];
        }
        if (empty($stake->onchain_investment_id)) {
            return ['success' => false, 'error' => 'no onchain investment'];
        }

        $log = $this->logTx([
            'member_id' => $stake->member_id,
            'tx_type' => 'sync_roi',
            'onchain_investment_id' => $stake->onchain_investment_id,
            'offchain_ref_id' => $stake->id,
            'amount' => (float) $stake->total_roi_paid,
            'payload' => [
                'roi_days' => (int) ($stake->roi_days_paid ?? 0),
            ],
        ]);

        $result = $this->call('syncRoi', [
            'investmentId' => (int) $stake->onchain_investment_id,
            'totalRoiGenerated' => (float) $stake->total_roi_paid,
            'roiDays' => (int) ($stake->roi_days_paid ?? 0),
        ]);

        $log->status = !empty($result['success']) ? 'success' : 'failed';
        $log->tx_hash = $result['txHash'] ?? null;
        $log->error_message = $result['error'] ?? null;
        $log->save();

        return $result;
    }

    public function creditAutoUpgradeOnChain(User $sponsor, User $from, float $amount): array
    {
        if (!$this->enabled() || !config('blockchain.sync_auto_upgrade_onchain')) {
            return ['success' => false, 'error' => 'disabled'];
        }

        $sponsorWallet = $this->normalizeWallet($sponsor->username);
        $fromWallet = $this->normalizeWallet($from->username);
        if ($sponsorWallet === '' || $amount <= 0) {
            return ['success' => false, 'error' => 'invalid args'];
        }

        $log = $this->logTx([
            'member_id' => $sponsor->id,
            'tx_type' => 'auto_upgrade_credit',
            'offchain_ref_id' => $from->id,
            'amount' => $amount,
        ]);

        $result = $this->call('creditAutoUpgrade', [
            'sponsor' => $sponsorWallet,
            'from' => $fromWallet,
            'amount' => $amount,
        ]);

        $log->status = !empty($result['success']) ? 'success' : 'failed';
        $log->tx_hash = $result['txHash'] ?? null;
        $log->error_message = $result['error'] ?? null;
        $log->save();

        return $result;
    }

    /**
     * Trigger on-chain auto-upgrade after Laravel has already activated locally.
     * Safe no-op when vault available balance is insufficient.
     */
    public function autoUpgradeOnChain(User $user, int $offchainStakeId = 0): array
    {
        if (!$this->enabled() || !config('blockchain.sync_auto_upgrade_onchain')) {
            return ['success' => false, 'error' => 'disabled'];
        }

        $wallet = $this->normalizeWallet($user->username);
        if ($wallet === '') {
            return ['success' => false, 'error' => 'no wallet'];
        }

        $log = $this->logTx([
            'member_id' => $user->id,
            'tx_type' => 'auto_upgrade',
            'offchain_ref_id' => $offchainStakeId,
        ]);

        $result = $this->call('autoUpgrade', [
            'user' => $wallet,
            'offchainStakeId' => $offchainStakeId,
        ]);

        $log->status = !empty($result['success']) ? 'success' : 'failed';
        $log->tx_hash = $result['txHash'] ?? null;
        $log->error_message = $result['error'] ?? null;
        $log->save();

        return $result;
    }

    public function processWithdrawalOnChain(User $user, float $grossAmount, int $withdrawalId): array
    {
        if (!$this->enabled() || !config('blockchain.withdrawals_via_vault')) {
            return ['success' => false, 'error' => 'disabled'];
        }

        $wallet = $this->normalizeWallet($user->username);
        if ($wallet === '') {
            return ['success' => false, 'error' => 'no wallet'];
        }

        $log = $this->logTx([
            'member_id' => $user->id,
            'tx_type' => 'withdrawal',
            'offchain_ref_id' => $withdrawalId,
            'amount' => $grossAmount,
        ]);

        $result = $this->call('processWithdrawal', [
            'user' => $wallet,
            'grossAmount' => $grossAmount,
            'offchainWithdrawalId' => $withdrawalId,
        ]);

        $log->status = !empty($result['success']) ? 'success' : 'failed';
        $log->tx_hash = $result['txHash'] ?? null;
        $log->error_message = $result['error'] ?? null;
        $log->save();

        return $result;
    }

    public function syncIncomeOnChain(User $user, float $amount, int $incomeType): array
    {
        if (!$this->enabled() || !config('blockchain.sync_roi_onchain')) {
            return ['success' => false, 'error' => 'disabled'];
        }

        $wallet = $this->normalizeWallet($user->username);
        if ($wallet === '' || $amount <= 0) {
            return ['success' => false, 'error' => 'invalid'];
        }

        $log = $this->logTx([
            'member_id' => $user->id,
            'tx_type' => 'income',
            'amount' => $amount,
            'payload' => ['income_type' => $incomeType],
        ]);

        $result = $this->call('syncIncome', [
            'user' => $wallet,
            'amount' => $amount,
            'incomeType' => $incomeType,
        ]);

        $log->status = !empty($result['success']) ? 'success' : 'failed';
        $log->tx_hash = $result['txHash'] ?? null;
        $log->error_message = $result['error'] ?? null;
        $log->save();

        return $result;
    }

    public function attachInvestResult(UserStaked $stake, array $investResult): void
    {
        if (empty($investResult['verified']) && empty($investResult['investmentId'])) {
            return;
        }

        $stake->onchain_investment_id = (int) ($investResult['investmentId'] ?? 0) ?: $stake->onchain_investment_id;
        $stake->chain_tx_hash = $investResult['txHash'] ?? $stake->chain_tx_hash;
        $stake->save();

        $member = User::find($stake->member_id);
        if ($member) {
            $member->chain_registered = 1;
            $member->save();
        }

        $this->logTx([
            'member_id' => $stake->member_id,
            'tx_type' => 'invest',
            'tx_hash' => $stake->chain_tx_hash,
            'onchain_investment_id' => $stake->onchain_investment_id,
            'offchain_ref_id' => $stake->id,
            'amount' => (float) $stake->paid_amount,
            'status' => 'success',
            'payload' => $investResult,
        ]);
    }

    public function frontendConfig(): array
    {
        return [
            'enabled' => $this->enabled(),
            'chain_id' => $this->chainId(),
            'rpc_url' => config('blockchain.rpc_url'),
            'vault_address' => $this->vaultAddress(),
            'usdt_address' => $this->usdtAddress(),
            'vault_abi' => $this->vaultAbiJson(),
            'explorer_tx_url' => config('blockchain.explorer_tx_url'),
            'require_onchain_invest' => (bool) config('blockchain.require_onchain_invest'),
        ];
    }
}
