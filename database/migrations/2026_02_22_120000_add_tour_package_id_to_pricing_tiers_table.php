<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pricing_tiers', function (Blueprint $table) {
            $table->unsignedBigInteger('tour_package_id')->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('pricing_tiers', function (Blueprint $table) {
            $table->dropColumn('tour_package_id');
        });
    }
};
