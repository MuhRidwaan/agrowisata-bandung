<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE bookings DROP FOREIGN KEY bookings_user_id_foreign');
        DB::statement('ALTER TABLE bookings MODIFY user_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE bookings ADD CONSTRAINT bookings_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE bookings DROP FOREIGN KEY bookings_user_id_foreign');
        DB::statement('ALTER TABLE bookings MODIFY user_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE bookings ADD CONSTRAINT bookings_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE');
    }
};
