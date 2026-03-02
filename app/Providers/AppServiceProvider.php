<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Load settings to config if table exists
        if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
            $settings = \App\Models\Setting::all();
            foreach ($settings as $setting) {
                if ($setting->key === 'app_name') {
                    config()->set('app.name', $setting->value);
                }
                if ($setting->category === 'payment') {
                    config()->set('midtrans.' . str_replace('midtrans_', '', $setting->key), $setting->value === 'true' ? true : ($setting->value === 'false' ? false : $setting->value));
                }
                if ($setting->category === 'email') {
                    if ($setting->key === 'mail_from_address') {
                        config()->set('mail.from.address', $setting->value);
                    }
                    if ($setting->key === 'mail_from_name') {
                        config()->set('mail.from.name', $setting->value);
                    }
                }
            }
        }
    }
}
