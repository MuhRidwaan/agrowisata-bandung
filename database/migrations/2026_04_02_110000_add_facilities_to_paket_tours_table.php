<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paket_tours', function (Blueprint $table) {
            $table->json('facilities')->nullable()->after('aktivitas');
        });
    }

    public function down(): void
    {
        Schema::table('paket_tours', function (Blueprint $table) {
            $table->dropColumn('facilities');
        });
    }
};
