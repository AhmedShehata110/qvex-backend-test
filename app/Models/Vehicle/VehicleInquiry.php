<?php

namespace App\Models\Vehicle;

use App\Models\BaseModel;
use App\Models\User;
use App\Models\Vendor\Vendor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleInquiry extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'user_id',
        'vendor_id',
        'inquiry_type',
        'subject',
        'message',
        'contact_preferences',
        'preferred_contact_time',
        'status',
        'vendor_notes',
        'responded_at',
    ];

    protected $casts = [
        'contact_preferences' => 'array',
        'responded_at' => 'timestamp',
    ];

    protected $translatable = [];

    // Inquiry type constants
    const TYPE_GENERAL = 'general';

    const TYPE_TEST_DRIVE = 'test_drive';

    const TYPE_PRICE_NEGOTIATION = 'price_negotiation';

    const TYPE_INSPECTION = 'inspection';

    // Status constants
    const STATUS_NEW = 'new';

    const STATUS_CONTACTED = 'contacted';

    const STATUS_IN_PROGRESS = 'in_progress';

    const STATUS_CLOSED = 'closed';

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get inquiry types with labels
     */
    public static function getInquiryTypes(): array
    {
        return [
            self::TYPE_GENERAL => 'General Inquiry',
            self::TYPE_TEST_DRIVE => 'Test Drive Request',
            self::TYPE_PRICE_NEGOTIATION => 'Price Negotiation',
            self::TYPE_INSPECTION => 'Inspection Request',
        ];
    }

    /**
     * Get status options with labels
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_NEW => 'New',
            self::STATUS_CONTACTED => 'Contacted',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_CLOSED => 'Closed',
        ];
    }

    /**
     * Mark as contacted
     */
    public function markAsContacted(): bool
    {
        return $this->update([
            'status' => self::STATUS_CONTACTED,
            'responded_at' => now(),
        ]);
    }

    /**
     * Mark as in progress
     */
    public function markAsInProgress(): bool
    {
        return $this->update(['status' => self::STATUS_IN_PROGRESS]);
    }

    /**
     * Mark as closed
     */
    public function markAsClosed(): bool
    {
        return $this->update(['status' => self::STATUS_CLOSED]);
    }

    /**
     * Scope to get inquiries by status
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get new inquiries
     */
    public function scopeNew($query)
    {
        return $query->where('status', self::STATUS_NEW);
    }

    /**
     * Scope to get inquiries by type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('inquiry_type', $type);
    }
}
