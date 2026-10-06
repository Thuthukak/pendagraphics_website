<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['group', 'key', 'value'];

    protected $casts = [
        'value' => 'array',
    ];

    /**
     * Cache key for a given group/key pair.
     */
    protected static function cacheKey(string $group, string $key): string
    {
        return "settings:{$group}:{$key}";
    }

    /**
     * Get a setting's value. Returns $default when the row doesn't exist
     * (or when the stored value is null).
     */
    public static function get(string $group, string $key, mixed $default = null): mixed
    {
        $value = Cache::rememberForever(static::cacheKey($group, $key), function () use ($group, $key) {
            return static::query()
                ->where('group', $group)
                ->where('key', $key)
                ->value('value');
        });

        return $value ?? $default;
    }

    /**
     * Get every key/value pair in a group as an associative array.
     */
    public static function group(string $group): array
    {
        return static::query()
            ->where('group', $group)
            ->get(['key', 'value'])
            ->pluck('value', 'key')
            ->toArray();
    }

    /**
     * Create or update a single setting value.
     */
    public static function set(string $group, string $key, mixed $value): self
    {
        $setting = static::updateOrCreate(
            ['group' => $group, 'key' => $key],
            ['value' => $value]
        );

        Cache::forget(static::cacheKey($group, $key));

        return $setting;
    }

    protected static function booted(): void
    {
        static::saved(fn (self $setting) => Cache::forget(static::cacheKey($setting->group, $setting->key)));
        static::deleted(fn (self $setting) => Cache::forget(static::cacheKey($setting->group, $setting->key)));
    }
}