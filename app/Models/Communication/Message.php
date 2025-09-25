<?php

namespace App\Models\Communication;

use App\Models\BaseModel;
use App\Models\User;
use App\Models\Vehicle\Vehicle;
use App\Models\Vendor\Vendor;
use App\Traits\AuditableTrait;
use Database\Factories\MessageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends BaseModel
{
    use AuditableTrait, HasFactory, SoftDeletes;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return MessageFactory::new();
    }

    protected $fillable = [
        'sender_id',
        'recipient_id',
        'vehicle_id',
        'vendor_id',
        'conversation_id',
        'parent_id',
        'subject',
        'body',
        'message_type',
        'priority',
        'status',
        'attachments',
        'metadata',
        'is_read',
        'is_starred',
        'is_archived',
        'read_at',
        'replied_at',
        'scheduled_at',
        'auto_response',
        'template_used',
        'source',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'attachments' => 'array',
        'metadata' => 'array',
        'is_read' => 'boolean',
        'is_starred' => 'boolean',
        'is_archived' => 'boolean',
        'auto_response' => 'boolean',
        'read_at' => 'timestamp',
        'replied_at' => 'timestamp',
        'scheduled_at' => 'timestamp',
    ];

    protected $translatable = [
        'subject',
        'body',
    ];

    // Message type constants
    const TYPE_INQUIRY = 'inquiry';

    const TYPE_RESPONSE = 'response';

    const TYPE_FOLLOW_UP = 'follow_up';

    const TYPE_NOTIFICATION = 'notification';

    const TYPE_SYSTEM = 'system';

    const TYPE_AUTO_RESPONSE = 'auto_response';

    // Priority constants
    const PRIORITY_LOW = 'low';

    const PRIORITY_NORMAL = 'normal';

    const PRIORITY_HIGH = 'high';

    const PRIORITY_URGENT = 'urgent';

    // Status constants
    const STATUS_DRAFT = 'draft';

    const STATUS_SENT = 'sent';

    const STATUS_DELIVERED = 'delivered';

    const STATUS_READ = 'read';

    const STATUS_REPLIED = 'replied';

    const STATUS_FAILED = 'failed';

    // Source constants
    const SOURCE_WEBSITE = 'website';

    const SOURCE_MOBILE_APP = 'mobile_app';

    const SOURCE_EMAIL = 'email';

    const SOURCE_PHONE = 'phone';

    const SOURCE_CHAT = 'chat';

    const SOURCE_ADMIN = 'admin';

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(static::class, 'parent_id')->orderBy('created_at');
    }

    public function conversation(): HasMany
    {
        return $this->hasMany(static::class, 'conversation_id')->orderBy('created_at');
    }

    /**
     * Get message types with labels
     */
    public static function getMessageTypes(): array
    {
        return [
            self::TYPE_INQUIRY => 'Customer Inquiry',
            self::TYPE_RESPONSE => 'Response',
            self::TYPE_FOLLOW_UP => 'Follow-up',
            self::TYPE_NOTIFICATION => 'Notification',
            self::TYPE_SYSTEM => 'System Message',
            self::TYPE_AUTO_RESPONSE => 'Auto Response',
        ];
    }

    /**
     * Get priority levels with labels
     */
    public static function getPriorities(): array
    {
        return [
            self::PRIORITY_LOW => 'Low',
            self::PRIORITY_NORMAL => 'Normal',
            self::PRIORITY_HIGH => 'High',
            self::PRIORITY_URGENT => 'Urgent',
        ];
    }

    /**
     * Get status options with labels
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_SENT => 'Sent',
            self::STATUS_DELIVERED => 'Delivered',
            self::STATUS_READ => 'Read',
            self::STATUS_REPLIED => 'Replied',
            self::STATUS_FAILED => 'Failed',
        ];
    }

    /**
     * Get sources with labels
     */
    public static function getSources(): array
    {
        return [
            self::SOURCE_WEBSITE => 'Website',
            self::SOURCE_MOBILE_APP => 'Mobile App',
            self::SOURCE_EMAIL => 'Email',
            self::SOURCE_PHONE => 'Phone',
            self::SOURCE_CHAT => 'Live Chat',
            self::SOURCE_ADMIN => 'Admin Panel',
        ];
    }

    /**
     * Get message type label
     */
    public function getMessageTypeLabelAttribute(): string
    {
        return static::getMessageTypes()[$this->message_type] ?? $this->message_type;
    }

    /**
     * Get priority label
     */
    public function getPriorityLabelAttribute(): string
    {
        return static::getPriorities()[$this->priority] ?? $this->priority;
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return static::getStatuses()[$this->status] ?? $this->status;
    }

    /**
     * Get source label
     */
    public function getSourceLabelAttribute(): string
    {
        return static::getSources()[$this->source] ?? $this->source;
    }

    /**
     * Check if message is read
     */
    public function isRead(): bool
    {
        return $this->is_read;
    }

    /**
     * Check if message is starred
     */
    public function isStarred(): bool
    {
        return $this->is_starred;
    }

    /**
     * Check if message is archived
     */
    public function isArchived(): bool
    {
        return $this->is_archived;
    }

    /**
     * Check if message is high priority
     */
    public function isHighPriority(): bool
    {
        return in_array($this->priority, [self::PRIORITY_HIGH, self::PRIORITY_URGENT]);
    }

    /**
     * Check if message is an inquiry
     */
    public function isInquiry(): bool
    {
        return $this->message_type === self::TYPE_INQUIRY;
    }

    /**
     * Check if message is a response
     */
    public function isResponse(): bool
    {
        return $this->message_type === self::TYPE_RESPONSE;
    }

    /**
     * Check if message has attachments
     */
    public function hasAttachments(): bool
    {
        return ! empty($this->attachments);
    }

    /**
     * Check if message is scheduled
     */
    public function isScheduled(): bool
    {
        return $this->scheduled_at && $this->scheduled_at->isFuture();
    }

    /**
     * Get formatted timestamp
     */
    public function getFormattedTimestampAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get conversation thread
     */
    public function getConversationThread(): array
    {
        if ($this->conversation_id) {
            return static::where('conversation_id', $this->conversation_id)
                ->orderBy('created_at')
                ->get()
                ->toArray();
        }

        return [$this->toArray()];
    }

    /**
     * Get response time if replied
     */
    public function getResponseTimeAttribute(): ?string
    {
        if (! $this->replied_at) {
            return null;
        }

        return $this->created_at->diffForHumans($this->replied_at, true);
    }

    /**
     * Mark as read
     */
    public function markAsRead(): bool
    {
        return $this->update([
            'is_read' => true,
            'read_at' => now(),
            'status' => self::STATUS_READ,
        ]);
    }

    /**
     * Mark as unread
     */
    public function markAsUnread(): bool
    {
        return $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    /**
     * Star message
     */
    public function star(): bool
    {
        return $this->update(['is_starred' => true]);
    }

    /**
     * Unstar message
     */
    public function unstar(): bool
    {
        return $this->update(['is_starred' => false]);
    }

    /**
     * Archive message
     */
    public function archive(): bool
    {
        return $this->update(['is_archived' => true]);
    }

    /**
     * Unarchive message
     */
    public function unarchive(): bool
    {
        return $this->update(['is_archived' => false]);
    }

    /**
     * Reply to message
     */
    public function reply(array $data): static
    {
        $replyData = array_merge($data, [
            'parent_id' => $this->id,
            'conversation_id' => $this->conversation_id ?? $this->id,
            'vehicle_id' => $this->vehicle_id,
            'vendor_id' => $this->vendor_id,
            'recipient_id' => $this->sender_id,
            'message_type' => self::TYPE_RESPONSE,
        ]);

        $reply = static::create($replyData);

        // Update original message
        $this->update([
            'status' => self::STATUS_REPLIED,
            'replied_at' => now(),
        ]);

        return $reply;
    }

    /**
     * Send message
     */
    public function send(): bool
    {
        return $this->update([
            'status' => self::STATUS_SENT,
            'scheduled_at' => null,
        ]);
    }

    /**
     * Mark as failed
     */
    public function markAsFailed(): bool
    {
        return $this->update(['status' => self::STATUS_FAILED]);
    }

    /**
     * Set priority
     */
    public function setPriority(string $priority): bool
    {
        return $this->update(['priority' => $priority]);
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeStarred($query)
    {
        return $query->where('is_starred', true);
    }

    public function scopeArchived($query)
    {
        return $query->where('is_archived', true);
    }

    public function scopeNotArchived($query)
    {
        return $query->where('is_archived', false);
    }

    public function scopeInbox($query)
    {
        return $query->where('is_archived', false);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('message_type', $type);
    }

    public function scopeInquiries($query)
    {
        return $query->where('message_type', self::TYPE_INQUIRY);
    }

    public function scopeResponses($query)
    {
        return $query->where('message_type', self::TYPE_RESPONSE);
    }

    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', [self::PRIORITY_HIGH, self::PRIORITY_URGENT]);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeSent($query)
    {
        return $query->where('status', self::STATUS_SENT);
    }

    public function scopeByConversation($query, int $conversationId)
    {
        return $query->where('conversation_id', $conversationId);
    }

    public function scopeByVehicle($query, int $vehicleId)
    {
        return $query->where('vehicle_id', $vehicleId);
    }

    public function scopeByVendor($query, int $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    public function scopeFromSender($query, int $senderId)
    {
        return $query->where('sender_id', $senderId);
    }

    public function scopeToRecipient($query, int $recipientId)
    {
        return $query->where('recipient_id', $recipientId);
    }

    public function scopeScheduled($query)
    {
        return $query->whereNotNull('scheduled_at')
            ->where('scheduled_at', '>', now());
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeUnanswered($query, int $hours = 24)
    {
        return $query->where('message_type', self::TYPE_INQUIRY)
            ->where('status', '!=', self::STATUS_REPLIED)
            ->where('created_at', '<=', now()->subHours($hours));
    }

    public function scopeWithAttachments($query)
    {
        return $query->whereNotNull('attachments')
            ->whereJsonLength('attachments', '>', 0);
    }

    public function scopeAutoResponses($query)
    {
        return $query->where('auto_response', true);
    }

    public function scopeBySource($query, string $source)
    {
        return $query->where('source', $source);
    }
}
