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

        $settings = [
            [
                'key' => 'enable_midtrans',
                'value' => 'true',
                'category' => 'payment',
                'label' => 'Enable Midtrans',
                'type' => 'checkbox',
            ],
            [
                'key' => 'enable_manual_payment',
                'value' => 'true',
                'category' => 'payment',
                'label' => 'Enable Manual Transfer',
                'type' => 'checkbox',
            ],
            [
                'key' => 'manual_payment_bank_name',
                'value' => 'Bank BCA',
                'category' => 'payment',
                'label' => 'Manual Payment Bank Name',
                'type' => 'text',
            ],
            [
                'key' => 'manual_payment_account_number',
                'value' => '1234567890',
                'category' => 'payment',
                'label' => 'Manual Payment Account Number',
                'type' => 'text',
            ],
            [
                'key' => 'manual_payment_account_name',
                'value' => 'PT Agrowisata Bandung',
                'category' => 'payment',
                'label' => 'Manual Payment Account Name',
                'type' => 'text',
            ],
            [
                'key' => 'manual_payment_instructions',
                'value' => 'Silakan transfer sesuai total tagihan, lalu kirim bukti transfer ke admin/vendor agar pembayaran dapat diverifikasi secara manual.',
                'category' => 'payment',
                'label' => 'Manual Payment Instructions',
                'type' => 'textarea',
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                array_merge($setting, [
                    'updated_at' => now(),
                    'created_at' => now(),
                ])
            );
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        DB::table('settings')
            ->whereIn('key', [
                'enable_midtrans',
                'enable_manual_payment',
                'manual_payment_bank_name',
                'manual_payment_account_number',
                'manual_payment_account_name',
                'manual_payment_instructions',
            ])
            ->delete();
    }
};
