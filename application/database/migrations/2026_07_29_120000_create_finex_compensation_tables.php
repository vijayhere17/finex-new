<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'auto_upgrade_balance')) {
                $table->decimal('auto_upgrade_balance', 18, 4)->default(0)->after('direct_roi_percent');
            }
            if (!Schema::hasColumn('users', 'current_slot')) {
                $table->unsignedTinyInteger('current_slot')->default(0)->after('auto_upgrade_balance');
            }
            if (!Schema::hasColumn('users', 'next_slot')) {
                $table->unsignedTinyInteger('next_slot')->default(1)->after('current_slot');
            }
        });

        Schema::table('staked_users', function (Blueprint $table) {
            if (!Schema::hasColumn('staked_users', 'roi_days_paid')) {
                $table->unsignedInteger('roi_days_paid')->default(0)->after('total_roi_paid');
            }
            if (!Schema::hasColumn('staked_users', 'max_roi_days')) {
                $table->unsignedInteger('max_roi_days')->default(300)->after('roi_days_paid');
            }
            if (!Schema::hasColumn('staked_users', 'slot_number')) {
                $table->unsignedTinyInteger('slot_number')->nullable()->after('kit_id');
            }
        });

        if (!Schema::hasTable('daily_roi_logs')) {
            Schema::create('daily_roi_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('member_id')->index();
                $table->unsignedBigInteger('stake_id')->index();
                $table->decimal('stake_amount', 18, 4)->default(0);
                $table->decimal('roi_percent', 8, 2)->default(0);
                $table->decimal('amount', 18, 4)->default(0);
                $table->date('roi_date')->index();
                $table->unsignedInteger('day_number')->default(0);
                $table->timestamps();
                $table->unique(['stake_id', 'roi_date'], 'daily_roi_stake_date_unique');
            });
        }

        if (!Schema::hasTable('level_roi_logs')) {
            Schema::create('level_roi_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('member_id')->index();
                $table->unsignedBigInteger('from_id')->index();
                $table->unsignedBigInteger('daily_roi_log_id')->nullable()->index();
                $table->unsignedTinyInteger('level')->default(0);
                $table->decimal('percent', 8, 2)->default(0);
                $table->decimal('base_amount', 18, 4)->default(0);
                $table->decimal('amount', 18, 4)->default(0);
                $table->date('roi_date')->index();
                $table->timestamps();
                $table->unique(['member_id', 'from_id', 'level', 'roi_date', 'daily_roi_log_id'], 'level_roi_unique');
            });
        }

        if (!Schema::hasTable('auto_upgrade_logs')) {
            Schema::create('auto_upgrade_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('member_id')->index();
                $table->string('event_type', 32); // credit | activate | upline_income
                $table->unsignedTinyInteger('slot_number')->nullable();
                $table->decimal('amount', 18, 4)->default(0);
                $table->decimal('balance_after', 18, 4)->default(0);
                $table->unsignedBigInteger('from_id')->nullable();
                $table->unsignedBigInteger('stake_id')->nullable();
                $table->string('description', 255)->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('spillover_logs')) {
            Schema::create('spillover_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('member_id')->index();
                $table->unsignedBigInteger('from_sponsor_id')->nullable();
                $table->unsignedBigInteger('to_sponsor_id')->nullable();
                $table->string('reason', 255)->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('spillover_logs');
        Schema::dropIfExists('auto_upgrade_logs');
        Schema::dropIfExists('level_roi_logs');
        Schema::dropIfExists('daily_roi_logs');

        Schema::table('staked_users', function (Blueprint $table) {
            foreach (['roi_days_paid', 'max_roi_days', 'slot_number'] as $col) {
                if (Schema::hasColumn('staked_users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('users', function (Blueprint $table) {
            foreach (['auto_upgrade_balance', 'current_slot', 'next_slot'] as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
