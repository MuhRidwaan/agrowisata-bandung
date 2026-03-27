<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Try to add the foreign key constraint; if it already exists, silently ignore
        try {
            Schema::table('paket_tour_umkm', function (Blueprint $table) {
                $table->foreign('umkm_product_id')
                      ->references('id')
                      ->on('umkm_products')
                      ->cascadeOnDelete();
            });
        } catch (\Exception $e) {
            // If constraint already exists (errno 121), silently ignore
            if (strpos($e->getMessage(), 'errno: 121') === false && 
                strpos($e->getMessage(), 'Duplicate key') === false) {
                throw $e;
            }
        }
    }

    public function down(): void
    {
        Schema::table('paket_tour_umkm', function (Blueprint $table) {
            $table->dropForeign(['umkm_product_id']);
        });
    }
};