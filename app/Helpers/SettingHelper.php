<?php

use App\Models\Setting;

if (!function_exists('get_setting')) {
    /**
     * Get setting value by key
     */
    function get_setting($key, $default = null)
    {
        return Setting::getValue($key, $default);
    }
}

if (!function_exists('storage_asset_url')) {
    /**
     * Resolve a public URL for assets that may live in public/, public/storage/,
     * or storage/app/public without requiring storage:link.
     *
     * @param string|null $path
     * @param string|null $default
     * @return string|null
     */
    function storage_asset_url($path, $default = null)
    {
        if (!$path) {
            return $default;
        }

        $path = trim($path);

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            $parsedUrl = parse_url($path);
            $host = strtolower((string) ($parsedUrl['host'] ?? ''));
            $urlPath = ltrim((string) ($parsedUrl['path'] ?? ''), '/');

            // Normalize app-local URLs so deployed environments don't keep pointing to localhost.
            if (in_array($host, ['127.0.0.1', 'localhost'], true) || str_starts_with($urlPath, 'storage/') || str_starts_with($urlPath, 'media/storage/')) {
                $path = $urlPath;
            } else {
                return $path;
            }
        }

        $normalizedPath = ltrim(str_replace('\\', '/', $path), '/');

        if (str_starts_with($normalizedPath, 'media/storage/')) {
            $normalizedPath = substr($normalizedPath, 14);
        }

        if (file_exists(public_path($normalizedPath))) {
            return asset($normalizedPath);
        }

        $storagePath = str_starts_with($normalizedPath, 'storage/')
            ? substr($normalizedPath, 8)
            : $normalizedPath;

        if (file_exists(public_path('storage/' . $storagePath))) {
            return asset('storage/' . $storagePath);
        }

        if (file_exists(storage_path('app/public/' . $storagePath))) {
            return route('public.storage', ['path' => $storagePath]);
        }

        if ($storagePath !== '') {
            return route('public.storage', ['path' => $storagePath]);
        }

        return $default;
    }
}

if (!function_exists('setting_asset_url')) {
    /**
     * Resolve a setting file URL with a frontend fallback asset.
     *
     * @param string $key
     * @param string $fallbackAsset
     * @return string
     */
    function setting_asset_url($key, $fallbackAsset = 'frontend/img/logo.png')
    {
        return storage_asset_url(get_setting($key), asset($fallbackAsset)) ?? asset($fallbackAsset);
    }
}
