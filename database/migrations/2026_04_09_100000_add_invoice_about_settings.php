<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $settings = [
            ['key' => 'invoice_company_logo',    'value' => null,                          'category' => 'general', 'label' => 'Company Logo',          'type' => 'file'],
            ['key' => 'invoice_company_name',    'value' => 'Agrowisata Tour',             'category' => 'general', 'label' => 'Company Name',          'type' => 'text'],
            ['key' => 'invoice_company_address', 'value' => "Jl. Raya Pariwisata No. 123\nBandung, West Java", 'category' => 'general', 'label' => 'Company Address', 'type' => 'textarea'],
            ['key' => 'invoice_company_phone',   'value' => '(022) 123-4567',              'category' => 'general', 'label' => 'Company Phone',         'type' => 'text'],
            ['key' => 'invoice_company_email',   'value' => 'info@agrowisata.com',         'category' => 'general', 'label' => 'Company Email',         'type' => 'text'],
            ['key' => 'invoice_footer_text',     'value' => 'Thank you for your payment. Please keep this invoice and show it to our staff during re-registration at the Agrowisata location.', 'category' => 'general', 'label' => 'Invoice Footer Text', 'type' => 'textarea'],
            ['key' => 'invoice_notes',           'value' => 'Thank you for your payment. Please keep this invoice and show it to our staff during re-registration at the Agrowisata location.', 'category' => 'general', 'label' => 'Important Notes',    'type' => 'textarea'],
            ['key' => 'about_title',             'value' => 'Tentang Agrowisata Bandung',  'category' => 'general', 'label' => 'About Title',           'type' => 'text'],
            ['key' => 'about_description',       'value' => 'AgroBandung adalah platform booking tour wisata yang menghubungkan wisatawan dengan berbagai destinasi agrowisata terbaik di Bandung.', 'category' => 'general', 'label' => 'About Description', 'type' => 'textarea'],
            ['key' => 'about_image',             'value' => null,                          'category' => 'general', 'label' => 'About Image',           'type' => 'file'],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                array_merge($setting, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', [
            'invoice_company_logo', 'invoice_company_name', 'invoice_company_address',
            'invoice_company_phone', 'invoice_company_email', 'invoice_footer_text',
            'invoice_notes', 'about_title', 'about_description', 'about_image',
        ])->delete();
    }
};
