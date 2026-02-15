<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('paket_tour_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paket_tour_id')->constrained('paket_tours')->onDelete('cascade');
            $table->string('path_foto');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paket_tour_photos');
    }
};
