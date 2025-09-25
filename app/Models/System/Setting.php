<?php

namespace App\Models\System;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    protected $translatable = ['description'];

    // Type constants
    const TYPE_STRING = 'string';

    const TYPE_INTEGER = 'integer';

    const TYPE_BOOLEAN = 'boolean';

    const TYPE_JSON = 'json';

    const TYPE_TEXT = 'text';

    /**
     * Get setting value with proper casting
     */
    public function getCastedValueAttribute()
    {
        switch ($this->type) {
            case self::TYPE_INTEGER:
                return (int) $this->value;
            case self::TYPE_BOOLEAN:
                return (bool) $this->value;
            case self::TYPE_JSON:
                return json_decode($this->value, true);
            default:
                return $this->value;
        }
    }

    /**
     * Get setting by key
     */
    public static function getValue(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();

        return $setting ? $setting->casted_value : $default;
    }

    /**
     * Set setting value
     */
    public static function setValue(string $key, $value, string $type = self::TYPE_STRING): bool
    {
        if ($type === self::TYPE_JSON) {
            $value = json_encode($value);
        }

        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type]
        ) ? true : false;
    }

    // Scopes
    public function scopeByGroup($query, string $group)
    {
        return $query->where('group', $group);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }
}
