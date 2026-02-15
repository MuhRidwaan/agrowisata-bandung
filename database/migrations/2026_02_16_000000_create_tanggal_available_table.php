<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tanggal_available', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paket_tour_id')->constrained('paket_tours')->onDelete('cascade');
            $table->date('tanggal');
            $table->integer('kuota')->default(0);
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tanggal_available');
    }
};
