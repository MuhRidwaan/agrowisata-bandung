<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pricing_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paket_tour_id')->constrained('paket_tours')->onDelete('cascade');
            $table->integer('qty_min');
            $table->integer('qty_max');
            $table->decimal('harga', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_tiers');
    }
};
