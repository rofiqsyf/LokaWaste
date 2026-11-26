<?php

use App\Models\SiteSetting;

if (!function_exists('site_setting')) {
    /**
     * Retrieve a dynamic website setting with safe default fallback.
     */
    function site_setting(string $key, mixed $default = null): mixed
    {
        return SiteSetting::get($key, $default);
    }
}
