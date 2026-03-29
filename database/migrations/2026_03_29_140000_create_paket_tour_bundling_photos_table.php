<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('paket_tour_bundling_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paket_tour_bundling_id')
                ->constrained('paket_tour_bundlings')
                ->cascadeOnDelete();
            $table->string('path_foto');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paket_tour_bundling_photos');
    }
};
