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
        if (!Schema::hasTable('pricing_tiers')) {
    
            Schema::create('pricing_tiers', function (Blueprint $table) {
                $table->id();
    
                $table->foreignId('tour_package_id')
                      ->constrained('paket_tours')
                      ->cascadeOnDelete();
    
                $table->string('name'); // Dewasa, Anak
                $table->integer('price');
    
                $table->timestamps();
            });
    
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing_tiers');
    }
};
