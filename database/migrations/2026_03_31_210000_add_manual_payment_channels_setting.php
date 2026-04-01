<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        DB::table('settings')->updateOrInsert(
            ['key' => 'manual_payment_channels'],
            [
                'key'        => 'manual_payment_channels',
                'value'      => json_encode([
                    [
                        'name'        => 'Transfer Bank BCA',
                        'type'        => 'bank_transfer',
                        'account_number' => '1234567890',
                        'account_name'   => 'PT Agrowisata Bandung',
                        'instructions'   => 'Transfer sesuai total tagihan, lalu upload bukti transfer.',
                        'is_active'   => true,
                    ],
                ]),
                'category'   => 'payment',
                'label'      => 'Manual Payment Channels (JSON)',
                'type'       => 'json_channels',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        DB::table('settings')->where('key', 'manual_payment_channels')->delete();
    }
};
