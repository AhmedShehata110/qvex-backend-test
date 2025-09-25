<?php

namespace App\Models\Customer;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAddress extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'label',
        'first_name',
        'last_name',
        'company',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'postal_code',
        'country',
        'latitude',
        'longitude',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    protected $translatable = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the full address as a formatted string
     */
    public function getFullAddressAttribute(): string
    {
        $address = [];

        if ($this->address_line_1) {
            $address[] = $this->address_line_1;
        }

        if ($this->address_line_2) {
            $address[] = $this->address_line_2;
        }

        if ($this->city) {
            $address[] = $this->city;
        }

        if ($this->state) {
            $address[] = $this->state;
        }

        if ($this->postal_code) {
            $address[] = $this->postal_code;
        }

        return implode(', ', $address);
    }

    /**
     * Get the full name (first_name + last_name)
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    /**
     * Scope to get default address
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope to get addresses by type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Set this address as the default one
     */
    public function setAsDefault(): bool
    {
        // Remove default flag from other addresses for this user
        static::where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        // Set this address as default
        return $this->update(['is_default' => true]);
    }
}
