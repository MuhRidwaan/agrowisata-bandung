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
    Schema::create('pricing_rules', function (Blueprint $table) {
        $table->id();

        // relasi ke paket
        $table->foreignId('tour_package_id')
              ->constrained()
              ->cascadeOnDelete();

        // range jumlah orang
        $table->integer('min_pax');
        $table->integer('max_pax');

        // tipe diskon
        $table->enum('discount_type', ['percent','nominal']);

        // nilai diskon
        $table->integer('discount_value');

        // opsional
        $table->string('description')->nullable();

        $table->timestamps();
    });
}

};
