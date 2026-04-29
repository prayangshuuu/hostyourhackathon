<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class SettingService
{
    private const CACHE_KEY = 'system_settings';
    private const CACHE_TTL = 60 * 60; // 60 minutes

    /**
     * Get all settings as a key-value array.
     */
    public function all(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return Setting::pluck('value', 'key')->toArray();
        });
    }

    /**
     * Get a specific setting value.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $settings = $this->all();

        if (!array_key_exists($key, $settings)) {
            return $default;
        }

        $value = $settings[$key];

        // For boolean settings
        $booleanKeys = [
            'allow_registration',
            'allow_multiple_hackathons',
            'enable_google_oauth',
            'enable_judging',
            'enable_leaderboard',
            'enable_submissions',
        ];

        if (in_array($key, $booleanKeys)) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        // For integer settings
        if (in_array($key, ['smtp_port', 'max_file_upload_mb'])) {
            return (int) $value;
        }

        // For encrypted setting
        if ($key === 'smtp_password' && !empty($value)) {
            try {
                return Crypt::decryptString($value);
            } catch (\Exception $e) {
                return $default;
            }
        }

        return $value;
    }

    /**
     * Set a setting value.
     */
    public function set(string $key, mixed $value): void
    {
        if ($key === 'smtp_password' && !empty($value)) {
            $value = Crypt::encryptString($value);
        }

        // Don't update password if it's empty in a form submission (keep old value)
        if ($key === 'smtp_password' && empty($value)) {
            return;
        }

        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => is_bool($value) ? ($value ? '1' : '0') : (string) $value]
        );

        Cache::forget(self::CACHE_KEY);
    }
}
