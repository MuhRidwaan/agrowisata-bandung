<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('paket_tours', function (Blueprint $table) {
            $table->unsignedBigInteger('vendor_id')->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('paket_tours', function (Blueprint $table) {
            $table->dropColumn('vendor_id');
        });
    }
};
