<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('paket_tours', function (Blueprint $table) {
            $table->boolean('has_minimum_person')->default(false)->after('harga_bundling');
            $table->unsignedInteger('minimum_person')->nullable()->after('has_minimum_person');
        });
    }

    public function down(): void
    {
        Schema::table('paket_tours', function (Blueprint $table) {
            $table->dropColumn(['has_minimum_person', 'minimum_person']);
        });
    }
};
