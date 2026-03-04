<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {

            $table->foreignId('paket_id')
                  ->after('id')
                  ->constrained('paket_tours')
                  ->onDelete('cascade');

        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {

            $table->dropForeign(['paket_id']);
            $table->dropColumn('paket_id');

        });
    }
};