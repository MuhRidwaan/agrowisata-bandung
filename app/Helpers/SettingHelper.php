<?php

use App\Models\Setting;

if (!function_exists('get_setting')) {
    /**
     * Get setting value by key
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function get_setting($key, $default = null)
    {
        return Setting::getValue($key, $default);
    }
}
