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
    Schema::create('monthly_roi_rates', function (Blueprint $table) {

        $table->id();

        $table->date('start_date');

        $table->date('end_date')->nullable();

        $table->decimal('daily_roi',8,2);

        $table->boolean('status')->default(1);

        $table->timestamps();

    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_r_o_i_rates');
    }
};
