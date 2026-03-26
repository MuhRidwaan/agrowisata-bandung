<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('paket_tour_umkm', function (Blueprint $table) {

            // LANGSUNG TAMBAH FK (tanpa drop)
            $table->foreign('umkm_product_id')
                  ->references('id')
                  ->on('umkm_products')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('paket_tour_umkm', function (Blueprint $table) {

            $table->dropForeign(['umkm_product_id']);
        });
    }
};