<?php

namespace App\Models\Vehicle;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleDocument extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'document_type',
        'title',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'issue_date',
        'expiry_date',
        'is_verified',
        'is_public',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'is_verified' => 'boolean',
        'is_public' => 'boolean',
    ];

    protected $translatable = ['title'];

    // Document type constants
    const TYPE_REGISTRATION = 'registration';

    const TYPE_INSURANCE = 'insurance';

    const TYPE_INSPECTION = 'inspection';

    const TYPE_SERVICE_RECORD = 'service_record';

    const TYPE_OWNERSHIP_TRANSFER = 'ownership_transfer';

    const TYPE_LOAN_CLEARANCE = 'loan_clearance';

    const TYPE_OTHER = 'other';

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get document types with labels
     */
    public static function getDocumentTypes(): array
    {
        return [
            self::TYPE_REGISTRATION => 'Registration',
            self::TYPE_INSURANCE => 'Insurance',
            self::TYPE_INSPECTION => 'Inspection',
            self::TYPE_SERVICE_RECORD => 'Service Record',
            self::TYPE_OWNERSHIP_TRANSFER => 'Ownership Transfer',
            self::TYPE_LOAN_CLEARANCE => 'Loan Clearance',
            self::TYPE_OTHER => 'Other',
        ];
    }

    /**
     * Get document type label
     */
    public function getDocumentTypeLabelAttribute(): string
    {
        return static::getDocumentTypes()[$this->document_type] ?? $this->document_type;
    }

    /**
     * Get file size in human readable format
     */
    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2).' '.$units[$i];
    }

    /**
     * Get file URL
     */
    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? asset('storage/'.$this->file_path) : null;
    }

    /**
     * Check if document is expired
     */
    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    /**
     * Check if document is expiring soon (within 30 days)
     */
    public function isExpiringSoon(): bool
    {
        return $this->expiry_date && $this->expiry_date->diffInDays(now()) <= 30 && ! $this->isExpired();
    }

    /**
     * Scope to get verified documents
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope to get public documents
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope to get documents by type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('document_type', $type);
    }

    /**
     * Scope to get expired documents
     */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('expiry_date')
            ->where('expiry_date', '<', now());
    }

    /**
     * Scope to get expiring soon documents
     */
    public function scopeExpiringSoon($query)
    {
        return $query->whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [now(), now()->addDays(30)]);
    }
}
