<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('paket_tour_bundlings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paket_tour_id')->constrained('paket_tours')->cascadeOnDelete();
            $table->string('label')->nullable();
            $table->unsignedInteger('people_count');
            $table->decimal('bundle_price', 15, 2);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['paket_tour_id', 'people_count'], 'paket_tour_bundlings_unique_people');
        });

        if (! Schema::hasTable('paket_tours')) {
            return;
        }

        $legacyBundlings = DB::table('paket_tours')
            ->select('id', 'harga_bundling', 'bundling_people')
            ->where('is_bundling_available', true)
            ->whereNotNull('harga_bundling')
            ->whereNotNull('bundling_people')
            ->get();

        foreach ($legacyBundlings as $bundling) {
            DB::table('paket_tour_bundlings')->updateOrInsert(
                [
                    'paket_tour_id' => $bundling->id,
                    'people_count' => $bundling->bundling_people,
                ],
                [
                    'label' => 'Bundling ' . $bundling->bundling_people . ' Orang',
                    'bundle_price' => $bundling->harga_bundling,
                    'description' => null,
                    'is_active' => true,
                    'sort_order' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('paket_tour_bundlings');
    }
};
