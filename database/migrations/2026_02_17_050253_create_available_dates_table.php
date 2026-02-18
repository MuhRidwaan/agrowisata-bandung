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
    Schema::create('available_dates', function (Blueprint $table) {
        $table->id();

        $table->foreignId('tour_package_id')
              ->constrained()
              ->cascadeOnDelete();

        $table->date('date');

        $table->integer('quota'); // kapasitas hari itu
        $table->integer('booked')->default(0); // sudah terisi

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('available_dates');
    }
};
