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

        DB::table('settings')->whereIn('key', [
            'manual_payment_bank_name',
            'manual_payment_account_number',
            'manual_payment_account_name',
            'manual_payment_instructions',
        ])->delete();
    }

    public function down(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        $settings = [
            ['key' => 'manual_payment_bank_name',      'value' => 'Bank BCA',              'label' => 'Manual Payment Bank Name',       'type' => 'text'],
            ['key' => 'manual_payment_account_number', 'value' => '1234567890',             'label' => 'Manual Payment Account Number',  'type' => 'text'],
            ['key' => 'manual_payment_account_name',   'value' => 'PT Agrowisata Bandung',  'label' => 'Manual Payment Account Name',    'type' => 'text'],
            ['key' => 'manual_payment_instructions',   'value' => 'Silakan transfer sesuai total tagihan.', 'label' => 'Manual Payment Instructions', 'type' => 'textarea'],
        ];

        foreach ($settings as $s) {
            DB::table('settings')->updateOrInsert(
                ['key' => $s['key']],
                array_merge($s, ['category' => 'payment', 'created_at' => now(), 'updated_at' => now()])
            );
        }
    }
};
