<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class SiteSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
    ];

    public const CACHE_KEY = 'lokawaste_site_settings_map';

    /**
     * Authoritative defaults for every configurable setting across the platform.
     */
    public static function defaults(): array
    {
        return [
            // Branding
            'site_name' => 'LokaWaste',
            'site_tagline' => 'Penyelamat Pangan',
            'site_description' => 'Platform penyelamatan pangan berlebih hiper-lokal untuk mencegah pemborosan makanan dan menyalurkan surplus pangan ke warga sekitar.',
            'site_logo_path' => null,
            'site_favicon_path' => null,

            // Home & Map / Catalog / Campaigns Copy
            'home_title' => 'LokaWaste — Peta Makanan Berlebih & Penyelamat Pangan',
            'home_sidebar_title' => 'Penyelamat Makanan',
            'home_search_placeholder' => 'Cari jalan, kelurahan, atau kecamatan (misal: Tembalang)...',
            'default_map_lat' => '-7.05194',
            'default_map_lng' => '110.43778',
            'default_map_radius_km' => '5',
            'catalog_hero_title' => 'Katalog Surplus Pangan',
            'catalog_hero_subtitle' => 'Jelajahi makanan layak konsumsi dari toko roti, resto, katering, dan warga sekitar. Pengambilan dilakukan langsung di titik lokasi via Peta Interaktif.',
            'campaigns_hero_title' => 'Galang Makanan Komunitas',
            'campaigns_hero_subtitle' => 'Bantu panti asuhan, pondok pesantren, dan warga rentan mendapatkan suplai makanan layak melalui relawan terverifikasi di setiap kecamatan.',

            // Operational & ESG Rules
            'max_active_claims_per_user' => '3',
            'no_show_penalty_threshold' => '3',
            'no_show_restriction_days' => '7',
            'esg_co2_per_portion' => '0.85',
            'esg_water_per_portion' => '320',
            'esg_value_per_portion' => '15000',

            // Contact & Global Announcement
            'contact_email' => 'halo@lokawaste.id',
            'contact_whatsapp' => '',
            'announcement_enabled' => '0',
            'announcement_text' => '',
            'announcement_url' => '',
        ];
    }

    /**
     * Return all stored settings merged on top of defaults.
     */
    public static function allMap(): array
    {
        $defaults = self::defaults();

        try {
            if (!Schema::hasTable('site_settings')) {
                return $defaults;
            }

            $stored = Cache::rememberForever(self::CACHE_KEY, function () {
                return self::query()->pluck('value', 'key')->toArray();
            });

            foreach ($stored as $k => $v) {
                if ($v !== null && $v !== '') {
                    $defaults[$k] = $v;
                } elseif (array_key_exists($k, $defaults) && in_array($k, ['site_logo_path', 'site_favicon_path', 'contact_whatsapp', 'announcement_enabled', 'announcement_text', 'announcement_url'], true)) {
                    $defaults[$k] = $v;
                }
            }

            return $defaults;
        } catch (\Throwable $e) {
            return $defaults;
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $map = self::allMap();
        if (array_key_exists($key, $map) && $map[$key] !== null && $map[$key] !== '') {
            return $map[$key];
        }

        return $default ?? (self::defaults()[$key] ?? null);
    }

    public static function setMany(array $pairs, string $group = 'general'): void
    {
        foreach ($pairs as $key => $value) {
            self::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value,
                    'group' => $group,
                ]
            );
        }

        self::clearCache();
    }

    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    public static function logoUrl(): ?string
    {
        $path = self::get('site_logo_path');
        if (!$path) {
            return null;
        }

        return Storage::disk('public')->url($path);
    }

    public static function faviconUrl(): ?string
    {
        $path = self::get('site_favicon_path');
        if (!$path) {
            return null;
        }

        return Storage::disk('public')->url($path);
    }
}
