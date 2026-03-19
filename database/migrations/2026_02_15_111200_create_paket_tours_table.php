<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('paket_tours', function (Blueprint $table) {
            $table->id();
            $table->string('nama_paket');
            $table->text('deskripsi')->nullable();
            // Gunakan tipe TIME, bukan string
            $table->time('jam_awal')->nullable();
            $table->time('jam_akhir')->nullable();
            $table->integer('kuota')->nullable();
            $table->decimal('harga_paket', 15, 2)->nullable();
            $table->json('aktivitas')->nullable();
            $table->boolean('is_bundling_available')->default(false);
            $table->decimal('harga_bundling', 15, 2)->nullable();
            $table->timestamps();
            $table->foreignId('vendor_id')->nullable()->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paket_tours');
    }

};
