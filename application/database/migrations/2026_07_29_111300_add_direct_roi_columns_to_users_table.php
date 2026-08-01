<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Direct ROI Income — store activation status + calculated daily ROI % per user.
 * Does not credit income; percentage is for display and future distribution module.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'activation_status')) {
                $table->string('activation_status', 32)->default('registered')->after('kit_id');
            }
            if (!Schema::hasColumn('users', 'qualified_active_directs')) {
                $table->unsignedInteger('qualified_active_directs')->default(0)->after('activation_status');
            }
            if (!Schema::hasColumn('users', 'direct_roi_percent')) {
                $table->decimal('direct_roi_percent', 5, 2)->default(0)->after('qualified_active_directs');
            }
        });

        // Existing activated members (have a package / slot) become active.
        if (Schema::hasColumn('users', 'kit_id') && Schema::hasColumn('users', 'activation_status')) {
            DB::table('users')
                ->where('kit_id', '>', 0)
                ->update(['activation_status' => 'active']);

            DB::table('users')
                ->where(function ($q) {
                    $q->whereNull('kit_id')->orWhere('kit_id', '<=', 0);
                })
                ->where(function ($q) {
                    $q->whereNull('activation_status')->orWhere('activation_status', '=', '');
                })
                ->update(['activation_status' => 'registered']);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'direct_roi_percent')) {
                $table->dropColumn('direct_roi_percent');
            }
            if (Schema::hasColumn('users', 'qualified_active_directs')) {
                $table->dropColumn('qualified_active_directs');
            }
            if (Schema::hasColumn('users', 'activation_status')) {
                $table->dropColumn('activation_status');
            }
        });
    }
};
