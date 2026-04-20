<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value'];

    /**
     * Get setting value by key.
     */
    public static function get(string $key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set setting value by key.
     */
    public static function set(string $key, $value): void
    {
        self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
        
        \Illuminate\Support\Facades\Cache::forget('app_settings');
    }

    /**
     * Get all settings as array.
     */
    public static function allSettings(): array
    {
        return \Illuminate\Support\Facades\Cache::remember('app_settings', 3600, function () {
            return self::pluck('value', 'key')->toArray();
        });
    }
}