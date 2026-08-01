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
        Schema::table('withdrawal_requests', function (Blueprint $table) {
            $table->tinyInteger('w_type')->default(0)->after('mode')->comment('0=income, 1=capital');
            $table->unsignedBigInteger('staked_user_id')->nullable()->after('member_id');
            $table->decimal('charge_percent', 5, 2)->nullable()->after('admin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('withdrawal_requests', function (Blueprint $table) {
            $table->dropColumn(['w_type', 'staked_user_id', 'charge_percent']);
        });
    }
};
