<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'group',
        'key',
        'value',
    ];

    public static function get(string $group, string $key, ?string $default = null): ?string
    {
        $setting = static::query()
            ->where('group', $group)
            ->where('key', $key)
            ->first();

        return $setting?->value ?? $default;
    }

    public static function set(string $group, string $key, ?string $value): self
    {
        return static::query()->updateOrCreate(
            ['group' => $group, 'key' => $key],
            ['value' => $value],
        );
    }
}
