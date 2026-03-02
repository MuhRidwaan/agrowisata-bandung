<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // BRANDING
            ['key' => 'app_name', 'value' => 'Jabar Agro', 'category' => 'branding', 'label' => 'Application Name', 'type' => 'text'],
            ['key' => 'app_logo', 'value' => null, 'category' => 'branding', 'label' => 'App Logo', 'type' => 'file'],
            
            // PAYMENT
            ['key' => 'midtrans_merchant_id', 'value' => 'YOUR_MERCHANT_ID', 'category' => 'payment', 'label' => 'Midtrans Merchant ID', 'type' => 'text'],
            ['key' => 'midtrans_server_key', 'value' => 'YOUR_SERVER_KEY', 'category' => 'payment', 'label' => 'Midtrans Server Key', 'type' => 'text'],
            ['key' => 'midtrans_client_key', 'value' => 'YOUR_CLIENT_KEY', 'category' => 'payment', 'label' => 'Midtrans Client Key', 'type' => 'text'],
            ['key' => 'midtrans_is_production', 'value' => 'false', 'category' => 'payment', 'label' => 'Midtrans Production Mode', 'type' => 'checkbox'],
            ['key' => 'midtrans_is_sanitized', 'value' => 'true', 'category' => 'payment', 'label' => 'Midtrans Sanitized', 'type' => 'checkbox'],
            ['key' => 'midtrans_is_3ds', 'value' => 'true', 'category' => 'payment', 'label' => 'Midtrans 3DS', 'type' => 'checkbox'],

            // EMAIL
            ['key' => 'enable_email_notification', 'value' => 'true', 'category' => 'email', 'label' => 'Send Email Notification', 'type' => 'checkbox'],
            ['key' => 'mail_from_address', 'value' => 'noreply@jabaragro.com', 'category' => 'email', 'label' => 'Mail From Address', 'type' => 'text'],
            ['key' => 'mail_from_name', 'value' => 'Jabar Agro Tour', 'category' => 'email', 'label' => 'Mail From Name', 'type' => 'text'],

            // OPERATIONAL
            ['key' => 'min_booking_hours', 'value' => '24', 'category' => 'general', 'label' => 'Min. Booking (Hours)', 'type' => 'number'],
            ['key' => 'booking_prefix', 'value' => 'AGR-', 'category' => 'general', 'label' => 'Booking Code Prefix', 'type' => 'text'],
        ];

        foreach ($settings as $setting) {
            \App\Models\Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
