<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Throwable;

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
        Paginator::useBootstrapFive();

        $this->loadDatabaseSettings();
    }

    private function loadDatabaseSettings(): void
    {
        try {
            if (! Schema::hasTable('settings')) {
                return;
            }

            $settings = Setting::all();

            foreach ($settings as $setting) {
                if ($setting->key === 'app_name') {
                    config()->set('app.name', $setting->value);
                }

                if ($setting->category === 'payment' && str_starts_with($setting->key, 'midtrans_')) {
                    config()->set(
                        'midtrans.' . str_replace('midtrans_', '', $setting->key),
                        $setting->value === 'true' ? true : ($setting->value === 'false' ? false : $setting->value)
                    );
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
        } catch (QueryException $exception) {
            $this->logSettingsLoadFailure($exception);
        } catch (Throwable $exception) {
            $this->logSettingsLoadFailure($exception);
        }
    }

    private function logSettingsLoadFailure(Throwable $exception): void
    {
        Log::warning('Skipping database settings during application boot.', [
            'error' => $exception->getMessage(),
        ]);
    }
}
