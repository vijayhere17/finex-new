<?php

namespace App\Services\Blockchain;

use App\Models\BlockchainTransaction;
use App\Models\User;
use App\Models\UserStaked;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

/**
 * Bridge between Laravel Finex management layer and FinexVault (BSC).
 * Uses Node operator-cli.js (ethers) via Symfony Process (Windows-safe).
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
     * Uses Symfony Process so env vars work on Windows (XAMPP/WAMP).
     */
    public function call(string $command, array $args = []): array
    {
        $script = config('blockchain.node_script');
        if (!$script || !is_file($script)) {
            return ['success' => false, 'error' => 'operator-cli.js missing at '.$script];
        }

        if (empty(config('blockchain.vault_address'))) {
            return ['success' => false, 'error' => 'FINEX_VAULT_ADDRESS is empty in .env'];
        }

        $node = $this->resolveNodeBinary();
        if ($node === '') {
            return [
                'success' => false,
                'error' => 'Node.js not found for PHP. Install Node and ensure it is on the system PATH (restart Apache after installing).',
            ];
        }

        $json = json_encode($args, JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            return ['success' => false, 'error' => 'Could not encode operator args'];
        }

        // Inherit current env, then override blockchain keys (critical on Windows).
        $env = $this->buildProcessEnv([
            'BSC_RPC_URL' => (string) config('blockchain.rpc_url'),
            'FINEX_VAULT_ADDRESS' => (string) config('blockchain.vault_address'),
            'BLOCKCHAIN_USDT_ADDRESS' => (string) config('blockchain.usdt_address'),
            'BLOCKCHAIN_OPERATOR_KEY' => (string) config('blockchain.operator_key'),
            'FINEX_ABI_PATH' => (string) config('blockchain.abi_path'),
            'SYSTEMROOT' => getenv('SYSTEMROOT') ?: (getenv('SystemRoot') ?: 'C:\\Windows'),
        ]);

        $process = new Process(
            [$node, $script, $command, $json],
            base_path('blockchain'),
            $env,
            null,
            120
        );

        try {
            $process->run();
        } catch (\Throwable $e) {
            Log::error('BlockchainService process failed', ['cmd' => $command, 'err' => $e->getMessage()]);
            return ['success' => false, 'error' => 'Operator process failed: '.$e->getMessage()];
        }

        $stdout = trim($process->getOutput());
        $stderr = trim($process->getErrorOutput());
        $combined = trim($stdout.($stderr !== '' ? "\n".$stderr : ''));

        $decoded = $this->decodeOperatorJson($stdout);
        if ($decoded === null) {
            $decoded = $this->decodeOperatorJson($combined);
        }

        if (!is_array($decoded)) {
            $snippet = mb_substr($combined !== '' ? $combined : '(empty output)', 0, 400);
            Log::warning('BlockchainService invalid response', [
                'cmd' => $command,
                'exit' => $process->getExitCode(),
                'out' => $combined,
                'node' => $node,
            ]);

            return [
                'success' => false,
                'error' => 'Invalid operator response: '.$snippet,
                'raw' => $combined,
                'exit_code' => $process->getExitCode(),
            ];
        }

        if (empty($decoded['success']) && empty($decoded['error']) && !$process->isSuccessful()) {
            $decoded['success'] = false;
            $decoded['error'] = $decoded['error'] ?? ('operator exit '.$process->getExitCode());
        }

        return $decoded;
    }

    protected function resolveNodeBinary(): string
    {
        $configured = (string) env('NODE_BINARY', '');
        if ($configured !== '' && is_file($configured)) {
            return $configured;
        }

        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

        if ($isWindows) {
            $where = [];
            @exec('where node 2>NUL', $where);
            foreach ($where as $line) {
                $path = trim($line);
                if ($path !== '' && is_file($path)) {
                    return $path;
                }
            }

            $candidates = [
                'C:\\Program Files\\nodejs\\node.exe',
                'C:\\Program Files (x86)\\nodejs\\node.exe',
                getenv('LOCALAPPDATA') ? getenv('LOCALAPPDATA').'\\Programs\\nodejs\\node.exe' : '',
            ];
            foreach ($candidates as $path) {
                if ($path && is_file($path)) {
                    return $path;
                }
            }

            return 'node';
        }

        $which = trim((string) @shell_exec('command -v node 2>/dev/null'));
        return $which !== '' ? $which : 'node';
    }

    protected function buildProcessEnv(array $overrides): array
    {
        $env = [];
        foreach ($_SERVER as $k => $v) {
            if (is_string($k) && is_scalar($v)) {
                $env[$k] = (string) $v;
            }
        }
        foreach ($_ENV as $k => $v) {
            if (is_string($k) && is_scalar($v)) {
                $env[$k] = (string) $v;
            }
        }
        foreach (['PATH', 'Path', 'SystemRoot', 'SYSTEMROOT', 'USERPROFILE', 'HOME', 'APPDATA', 'LOCALAPPDATA'] as $k) {
            $v = getenv($k);
            if ($v !== false && $v !== '') {
                $env[$k] = $v;
            }
        }

        foreach ($overrides as $k => $v) {
            if ($v === null || $v === '') {
                continue;
            }
            $env[$k] = $v;
        }

        return $env;
    }

    protected function decodeOperatorJson(string $output): ?array
    {
        $output = trim($output);
        if ($output === '') {
            return null;
        }

        $direct = json_decode($output, true);
        if (is_array($direct)) {
            return $direct;
        }

        // Operator may print warnings before JSON — take the last JSON object.
        if (preg_match_all('/\{(?:[^{}]|(?R))*\}/s', $output, $matches) && !empty($matches[0])) {
            $last = $matches[0][count($matches[0]) - 1];
            $decoded = json_decode($last, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        $start = strrpos($output, '{');
        $end = strrpos($output, '}');
        if ($start !== false && $end !== false && $end > $start) {
            $decoded = json_decode(substr($output, $start, $end - $start + 1), true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }

    protected function logTx(array $data): ?BlockchainTransaction
    {
        try {
            return BlockchainTransaction::create(array_merge([
                'status' => 'pending',
            ], $data));
        } catch (\Throwable $e) {
            // Table may not exist yet if SQL migration was not applied.
            Log::warning('blockchain_transactions log skipped: '.$e->getMessage());
            return null;
        }
    }

    protected function finishLog(?BlockchainTransaction $log, array $result): void
    {
        if ($log == null) {
            return;
        }
        try {
            $log->status = !empty($result['success']) ? 'success' : 'failed';
            $log->tx_hash = $result['txHash'] ?? null;
            $log->error_message = $result['error'] ?? null;
            $log->save();
        } catch (\Throwable $e) {
            Log::warning('blockchain_transactions update skipped: '.$e->getMessage());
        }
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

        $this->finishLog($log, $result);

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

        $this->finishLog($log, $result);

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

        $this->finishLog($log, $result);

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

        $this->finishLog($log, $result);

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

        $this->finishLog($log, $result);

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

    /**
     * If Laravel already activated slots (legacy admin path) but FinexVault is behind,
     * advance on-chain currentSlot/nextSlot so the next paid invest() succeeds.
     */
    public function syncMemberProgress(User $user, int $currentSlot): array
    {
        if (!$this->enabled()) {
            return ['success' => false, 'error' => 'disabled'];
        }

        $wallet = $this->normalizeWallet($user->username);
        if ($wallet === '') {
            return ['success' => false, 'error' => 'no wallet'];
        }

        $sponsorWallet = '';
        if ((int) $user->referral_id > 0) {
            $sponsor = User::find($user->referral_id);
            if ($sponsor) {
                $sponsorWallet = $this->normalizeWallet($sponsor->username);
            }
        }

        // Read chain state first — skip tx if already caught up.
        $onchain = $this->call('getMember', ['user' => $wallet]);
        if (!empty($onchain['success'])) {
            $chainCurrent = (int) ($onchain['currentSlot'] ?? 0);
            $chainNext = (int) ($onchain['nextSlot'] ?? 1);
            if ($chainCurrent >= $currentSlot) {
                return [
                    'success' => true,
                    'skipped' => true,
                    'currentSlot' => $chainCurrent,
                    'nextSlot' => $chainNext,
                ];
            }
        }

        $log = $this->logTx([
            'member_id' => $user->id,
            'tx_type' => 'sync_progress',
            'amount' => 0,
            'payload' => ['current_slot' => $currentSlot],
        ]);

        $result = $this->call('syncMemberProgress', [
            'user' => $wallet,
            'sponsor' => $sponsorWallet !== '' ? $sponsorWallet : '0x0000000000000000000000000000000000000000',
            'currentSlot' => $currentSlot,
        ]);

        $this->finishLog($log, $result);

        return $result;
    }
}
