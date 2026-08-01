<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('staked_users', function (Blueprint $table) {
            $table->unsignedBigInteger('roi_tier_id')->nullable()->after('kit_id');
            $table->dateTime('capital_withdrawn_at')->nullable()->after('is_deleted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staked_users', function (Blueprint $table) {
            $table->dropColumn(['roi_tier_id', 'capital_withdrawn_at']);
        });
    }
};
