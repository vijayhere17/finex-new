<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Minimal columns required to synchronize Finex off-chain ledgers with FinexVault.
 * Does not replace existing wallet / stake structure.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'sponsor_wallet_total')) {
                $table->decimal('sponsor_wallet_total', 18, 4)->default(0)->after('auto_upgrade_balance');
            }
            if (!Schema::hasColumn('users', 'auto_upgrade_used')) {
                $table->decimal('auto_upgrade_used', 18, 4)->default(0)->after('sponsor_wallet_total');
            }
            if (!Schema::hasColumn('users', 'chain_registered')) {
                $table->boolean('chain_registered')->default(false)->after('next_slot');
            }
        });

        Schema::table('staked_users', function (Blueprint $table) {
            if (!Schema::hasColumn('staked_users', 'onchain_investment_id')) {
                $table->unsignedBigInteger('onchain_investment_id')->nullable()->index()->after('slot_number');
            }
            if (!Schema::hasColumn('staked_users', 'unlocked_roi')) {
                $table->decimal('unlocked_roi', 18, 4)->default(0)->after('total_roi_paid');
            }
            if (!Schema::hasColumn('staked_users', 'withdrawn_roi')) {
                $table->decimal('withdrawn_roi', 18, 4)->default(0)->after('unlocked_roi');
            }
            if (!Schema::hasColumn('staked_users', 'qualifying_directs')) {
                $table->unsignedInteger('qualifying_directs')->default(0)->after('withdrawn_roi');
            }
            if (!Schema::hasColumn('staked_users', 'chain_tx_hash')) {
                $table->string('chain_tx_hash', 100)->nullable()->after('qualifying_directs');
            }
        });

        if (!Schema::hasTable('blockchain_transactions')) {
            Schema::create('blockchain_transactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('member_id')->nullable()->index();
                $table->string('tx_type', 48)->index(); // invest|sync_roi|auto_upgrade|withdrawal|income|qualifying_direct
                $table->string('tx_hash', 100)->nullable()->index();
                $table->unsignedBigInteger('onchain_investment_id')->nullable()->index();
                $table->unsignedBigInteger('offchain_ref_id')->nullable()->index();
                $table->decimal('amount', 18, 4)->default(0);
                $table->string('status', 24)->default('pending'); // pending|success|failed
                $table->json('payload')->nullable();
                $table->text('error_message')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('blockchain_transactions');

        Schema::table('staked_users', function (Blueprint $table) {
            foreach (['onchain_investment_id', 'unlocked_roi', 'withdrawn_roi', 'qualifying_directs', 'chain_tx_hash'] as $col) {
                if (Schema::hasColumn('staked_users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('users', function (Blueprint $table) {
            foreach (['sponsor_wallet_total', 'auto_upgrade_used', 'chain_registered'] as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
