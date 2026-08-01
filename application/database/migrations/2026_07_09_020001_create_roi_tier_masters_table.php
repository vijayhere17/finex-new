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
        Schema::create('roi_tier_masters', function (Blueprint $table) {
            $table->id();
            $table->decimal('min_amount', 18, 2);
            $table->decimal('max_amount', 18, 2)->nullable();
            $table->decimal('daily_percent', 6, 3);
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roi_tier_masters');
    }
};
