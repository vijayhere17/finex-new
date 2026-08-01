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
        Schema::create('turnover_reward_achievers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_id');
            $table->unsignedBigInteger('reward_id');
            $table->decimal('leg1_business', 18, 2)->default(0);
            $table->decimal('leg2_business', 18, 2)->default(0);
            $table->decimal('leg3_business', 18, 2)->default(0);
            $table->decimal('cash_reward', 18, 2);
            $table->timestamps();

            $table->unique(['member_id', 'reward_id'], 'member_reward_unique');
            $table->index('member_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('turnover_reward_achievers');
    }
};
