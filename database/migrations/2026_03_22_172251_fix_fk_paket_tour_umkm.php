<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('paket_tour_umkm') || ! Schema::hasTable('umkm_products')) {
            return;
        }

        $databaseName = DB::getDatabaseName();
        $foreignKeyName = 'paket_tour_umkm_umkm_product_id_foreign';

        $foreignKeyExists = DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', $databaseName)
            ->where('TABLE_NAME', 'paket_tour_umkm')
            ->where('CONSTRAINT_NAME', $foreignKeyName)
            ->where('CONSTRAINT_TYPE', 'FOREIGN KEY')
            ->exists();

        if ($foreignKeyExists) {
            return;
        }

        Schema::table('paket_tour_umkm', function (Blueprint $table) {
            $table->foreign('umkm_product_id')
                ->references('id')
                ->on('umkm_products')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('paket_tour_umkm')) {
            return;
        }

        Schema::table('paket_tour_umkm', function (Blueprint $table) {
            $table->dropForeign(['umkm_product_id']);
        });
    }
};
