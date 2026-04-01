<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL bisa memakai unique index existing untuk FK paket_tour_id.
        // Jadi kita buat index biasa dulu agar FK tetap punya index pengganti.
        Schema::table('paket_tour_bundlings', function (Blueprint $table) {
            $table->index('paket_tour_id', 'paket_tour_bundlings_paket_tour_id_index');
        });

        Schema::table('paket_tour_bundlings', function (Blueprint $table) {
            $table->dropUnique('paket_tour_bundlings_unique_people');
        });
    }

    public function down(): void
    {
        Schema::table('paket_tour_bundlings', function (Blueprint $table) {
            $table->unique(['paket_tour_id', 'people_count'], 'paket_tour_bundlings_unique_people');
        });

        Schema::table('paket_tour_bundlings', function (Blueprint $table) {
            $table->dropIndex('paket_tour_bundlings_paket_tour_id_index');
        });
    }
};
