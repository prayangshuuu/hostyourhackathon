<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class SettingService
{
    private string $cacheKey = 'system_settings';
    private int $cacheTtl = 3600; // 1 hour
    private bool $deferFlush = false;

    /**
     * @var array<int, string>
     */
    private array $encryptedKeys = [
        'smtp_password',
    ];

    /**
     * @var array<int, string>
     */
    private array $booleanKeys = [
        'allow_registration',
        'allow_multiple_hackathons',
        'enable_google_oauth',
        'enable_judging',
        'enable_leaderboard',
        'enable_submissions',
    ];

    /**
     * @var array<int, string>
     */
    private array $integerKeys = [
        'smtp_port',
        'max_file_upload_mb',
    ];

    public function all(): array
    {
        return Cache::remember($this->cacheKey, $this->cacheTtl, function () {
            return Setting::pluck('value', 'key')->toArray();
        });
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $settings = $this->all();

        if (!array_key_exists($key, $settings)) {
            return $default;
        }

        $value = $settings[$key];

        if (in_array($key, $this->encryptedKeys, true) && $value !== null && $value !== '') {
            try {
                $value = Crypt::decryptString($value);
            } catch (\Exception $e) {
                return $default;
            }
        }

        if (in_array($key, $this->booleanKeys, true)) {
            return filter_var($value, FILTER_VALIDATE_BOOL);
        }

        if (in_array($key, $this->integerKeys, true)) {
            return (int) $value;
        }

        return $value;
    }

    public function set(string $key, mixed $value): void
    {
        if ($value === null) {
            Setting::query()->where('key', $key)->delete();
            if (! $this->deferFlush) {
                $this->flush();
            }
            return;
        }

        $persistedValue = $value;

        if (in_array($key, $this->encryptedKeys, true)) {
            if ($value === '') {
                return;
            }

            $persistedValue = Crypt::encryptString((string) $value);
        } elseif (is_bool($value)) {
            $persistedValue = $value ? '1' : '0';
        } else {
            $persistedValue = (string) $value;
        }

        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $persistedValue]
        );

        if (! $this->deferFlush) {
            $this->flush();
        }
    }

    public function setMany(array $data): void
    {
        DB::transaction(function () use ($data) {
            $this->deferFlush = true;
            try {
                foreach ($data as $key => $value) {
                    $this->set((string) $key, $value);
                }
            } finally {
                $this->deferFlush = false;
            }
        });

        $this->flush();
    }

    public function flush(): void
    {
        Cache::forget($this->cacheKey);
    }
}
