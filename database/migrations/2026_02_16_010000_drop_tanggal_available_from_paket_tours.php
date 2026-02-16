<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        if (Schema::hasColumn('paket_tours', 'tanggal_available')) {

            Schema::table('paket_tours', function (Blueprint $table) {
                $table->dropColumn('tanggal_available');
            });

        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('paket_tours', 'tanggal_available')) {

            Schema::table('paket_tours', function (Blueprint $table) {
                $table->date('tanggal_available')->nullable();
            });

        }
    }

};
