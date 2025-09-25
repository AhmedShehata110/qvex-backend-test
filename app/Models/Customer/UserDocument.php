<?php

namespace App\Models\Customer;

use App\Models\BaseModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDocument extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'document_type',
        'document_number',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'verification_status',
        'rejection_reason',
        'verified_at',
        'verified_by',
        'expires_at',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'verified_at' => 'timestamp',
        'expires_at' => 'date',
    ];

    protected $translatable = [];

    // Document type constants
    const TYPE_NATIONAL_ID = 'national_id';

    const TYPE_PASSPORT = 'passport';

    const TYPE_DRIVER_LICENSE = 'driver_license';

    const TYPE_UTILITY_BILL = 'utility_bill';

    const TYPE_BANK_STATEMENT = 'bank_statement';

    const TYPE_OTHER = 'other';

    // Verification status constants
    const STATUS_PENDING = 'pending';

    const STATUS_VERIFIED = 'verified';

    const STATUS_REJECTED = 'rejected';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Get document types with labels
     */
    public static function getDocumentTypes(): array
    {
        return [
            self::TYPE_NATIONAL_ID => 'National ID',
            self::TYPE_PASSPORT => 'Passport',
            self::TYPE_DRIVER_LICENSE => 'Driver License',
            self::TYPE_UTILITY_BILL => 'Utility Bill',
            self::TYPE_BANK_STATEMENT => 'Bank Statement',
            self::TYPE_OTHER => 'Other',
        ];
    }

    /**
     * Get verification statuses with labels
     */
    public static function getVerificationStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_VERIFIED => 'Verified',
            self::STATUS_REJECTED => 'Rejected',
        ];
    }

    /**
     * Check if document is verified
     */
    public function isVerified(): bool
    {
        return $this->verification_status === self::STATUS_VERIFIED;
    }

    /**
     * Check if document is pending verification
     */
    public function isPending(): bool
    {
        return $this->verification_status === self::STATUS_PENDING;
    }

    /**
     * Check if document is rejected
     */
    public function isRejected(): bool
    {
        return $this->verification_status === self::STATUS_REJECTED;
    }

    /**
     * Check if document is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && Carbon::parse($this->expires_at)->isPast();
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
     * Get document type label
     */
    public function getDocumentTypeLabelAttribute(): string
    {
        return static::getDocumentTypes()[$this->document_type] ?? $this->document_type;
    }

    /**
     * Get verification status label
     */
    public function getVerificationStatusLabelAttribute(): string
    {
        return static::getVerificationStatuses()[$this->verification_status] ?? $this->verification_status;
    }

    /**
     * Verify the document
     */
    public function verify(User $verifier): bool
    {
        return $this->update([
            'verification_status' => self::STATUS_VERIFIED,
            'verified_at' => now(),
            'verified_by' => $verifier->id,
            'rejection_reason' => null,
        ]);
    }

    /**
     * Reject the document
     */
    public function reject(string $reason, User $verifier): bool
    {
        return $this->update([
            'verification_status' => self::STATUS_REJECTED,
            'rejection_reason' => $reason,
            'verified_at' => null,
            'verified_by' => $verifier->id,
        ]);
    }

    /**
     * Scope to get verified documents
     */
    public function scopeVerified($query)
    {
        return $query->where('verification_status', self::STATUS_VERIFIED);
    }

    /**
     * Scope to get pending documents
     */
    public function scopePending($query)
    {
        return $query->where('verification_status', self::STATUS_PENDING);
    }

    /**
     * Scope to get rejected documents
     */
    public function scopeRejected($query)
    {
        return $query->where('verification_status', self::STATUS_REJECTED);
    }

    /**
     * Scope to get documents by type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('document_type', $type);
    }
}
