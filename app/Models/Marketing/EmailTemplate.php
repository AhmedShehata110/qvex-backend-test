<?php

namespace App\Models\Marketing;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailTemplate extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'category',
        'type',
        'subject',
        'html_content',
        'text_content',
        'variables',
        'description',
        'status',
        'is_default',
        'preview_data',
        'created_by',
        'sender_name',
        'sender_email',
        'reply_to',
        'design_json',
        'usage_count',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_default' => 'boolean',
        'preview_data' => 'array',
        'design_json' => 'array',
        'usage_count' => 'integer',
    ];

    protected $translatable = [
        'name',
        'subject',
        'html_content',
        'text_content',
        'description',
    ];

    // Category constants
    const CATEGORY_TRANSACTIONAL = 'transactional';

    const CATEGORY_MARKETING = 'marketing';

    const CATEGORY_NOTIFICATION = 'notification';

    const CATEGORY_SYSTEM = 'system';

    // Type constants
    const TYPE_WELCOME = 'welcome';

    const TYPE_VERIFICATION = 'verification';

    const TYPE_PASSWORD_RESET = 'password_reset';

    const TYPE_INQUIRY_CONFIRMATION = 'inquiry_confirmation';

    const TYPE_BOOKING_CONFIRMATION = 'booking_confirmation';

    const TYPE_PAYMENT_CONFIRMATION = 'payment_confirmation';

    const TYPE_NEWSLETTER = 'newsletter';

    const TYPE_PROMOTIONAL = 'promotional';

    const TYPE_REMINDER = 'reminder';

    const TYPE_ALERT = 'alert';

    const TYPE_REVIEW_REQUEST = 'review_request';

    const TYPE_CUSTOM = 'custom';

    // Status constants
    const STATUS_DRAFT = 'draft';

    const STATUS_ACTIVE = 'active';

    const STATUS_INACTIVE = 'inactive';

    const STATUS_ARCHIVED = 'archived';

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get categories with labels
     */
    public static function getCategories(): array
    {
        return [
            self::CATEGORY_TRANSACTIONAL => 'Transactional',
            self::CATEGORY_MARKETING => 'Marketing',
            self::CATEGORY_NOTIFICATION => 'Notification',
            self::CATEGORY_SYSTEM => 'System',
        ];
    }

    /**
     * Get types with labels
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_WELCOME => 'Welcome Email',
            self::TYPE_VERIFICATION => 'Email Verification',
            self::TYPE_PASSWORD_RESET => 'Password Reset',
            self::TYPE_INQUIRY_CONFIRMATION => 'Inquiry Confirmation',
            self::TYPE_BOOKING_CONFIRMATION => 'Booking Confirmation',
            self::TYPE_PAYMENT_CONFIRMATION => 'Payment Confirmation',
            self::TYPE_NEWSLETTER => 'Newsletter',
            self::TYPE_PROMOTIONAL => 'Promotional',
            self::TYPE_REMINDER => 'Reminder',
            self::TYPE_ALERT => 'Alert',
            self::TYPE_REVIEW_REQUEST => 'Review Request',
            self::TYPE_CUSTOM => 'Custom',
        ];
    }

    /**
     * Get statuses with labels
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_ARCHIVED => 'Archived',
        ];
    }

    /**
     * Get category label
     */
    public function getCategoryLabelAttribute(): string
    {
        return static::getCategories()[$this->category] ?? $this->category;
    }

    /**
     * Get type label
     */
    public function getTypeLabelAttribute(): string
    {
        return static::getTypes()[$this->type] ?? $this->type;
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return static::getStatuses()[$this->status] ?? $this->status;
    }

    /**
     * Check if template is active
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE && $this->is_active;
    }

    /**
     * Check if template is default for its type
     */
    public function isDefault(): bool
    {
        return $this->is_default;
    }

    /**
     * Get available variables list
     */
    public function getAvailableVariablesAttribute(): array
    {
        return $this->variables ?? $this->getDefaultVariablesForType($this->type);
    }

    /**
     * Get default variables for template type
     */
    protected function getDefaultVariablesForType(string $type): array
    {
        $baseVariables = [
            'user_name',
            'user_email',
            'site_name',
            'site_url',
            'support_email',
            'current_date',
        ];

        $typeSpecificVariables = [
            self::TYPE_WELCOME => ['activation_link', 'login_url'],
            self::TYPE_VERIFICATION => ['verification_link', 'verification_code'],
            self::TYPE_PASSWORD_RESET => ['reset_link', 'reset_code'],
            self::TYPE_INQUIRY_CONFIRMATION => ['inquiry_id', 'vehicle_name', 'vehicle_url'],
            self::TYPE_BOOKING_CONFIRMATION => ['booking_id', 'booking_date', 'vehicle_name'],
            self::TYPE_PAYMENT_CONFIRMATION => ['payment_amount', 'payment_method', 'transaction_id'],
            self::TYPE_REVIEW_REQUEST => ['purchase_date', 'vehicle_name', 'review_link'],
        ];

        return array_merge($baseVariables, $typeSpecificVariables[$type] ?? []);
    }

    /**
     * Replace variables in content
     */
    public function renderContent(array $data = []): array
    {
        $mergedData = array_merge($this->preview_data ?? [], $data);

        return [
            'subject' => $this->replaceVariables($this->subject, $mergedData),
            'html_content' => $this->replaceVariables($this->html_content, $mergedData),
            'text_content' => $this->replaceVariables($this->text_content, $mergedData),
        ];
    }

    /**
     * Replace variables in text
     */
    protected function replaceVariables(string $content, array $data): string
    {
        foreach ($data as $key => $value) {
            $content = str_replace('{{'.$key.'}}', $value, $content);
            $content = str_replace('{{ '.$key.' }}', $value, $content);
        }

        return $content;
    }

    /**
     * Extract variables from content
     */
    public function extractVariables(): array
    {
        $content = $this->html_content.' '.$this->text_content.' '.$this->subject;

        preg_match_all('/\{\{\s*([^}]+)\s*\}\}/', $content, $matches);

        return array_unique($matches[1] ?? []);
    }

    /**
     * Validate template content
     */
    public function validateContent(): array
    {
        $errors = [];

        // Check for required fields
        if (empty($this->subject)) {
            $errors[] = 'Subject is required';
        }

        if (empty($this->html_content) && empty($this->text_content)) {
            $errors[] = 'At least one of HTML or text content is required';
        }

        // Check for unmatched variables
        $availableVars = $this->available_variables;
        $usedVars = $this->extractVariables();
        $unavailableVars = array_diff($usedVars, $availableVars);

        if (! empty($unavailableVars)) {
            $errors[] = 'Undefined variables: '.implode(', ', $unavailableVars);
        }

        return $errors;
    }

    /**
     * Set as default template for type
     */
    public function setAsDefault(): bool
    {
        // Unset other default templates of same type
        static::where('type', $this->type)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);

        return $this->update(['is_default' => true]);
    }

    /**
     * Activate template
     */
    public function activate(): bool
    {
        return $this->update(['status' => self::STATUS_ACTIVE]);
    }

    /**
     * Deactivate template
     */
    public function deactivate(): bool
    {
        return $this->update(['status' => self::STATUS_INACTIVE]);
    }

    /**
     * Archive template
     */
    public function archive(): bool
    {
        return $this->update(['status' => self::STATUS_ARCHIVED]);
    }

    /**
     * Increment usage count
     */
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    /**
     * Duplicate template
     */
    public function duplicate(string $suffix = ' - Copy'): static
    {
        $attributes = $this->toArray();
        unset($attributes['id'], $attributes['created_at'], $attributes['updated_at']);

        $attributes['name'] .= $suffix;
        $attributes['slug'] .= '-copy';
        $attributes['status'] = self::STATUS_DRAFT;
        $attributes['is_default'] = false;
        $attributes['usage_count'] = 0;

        return static::create($attributes);
    }

    /**
     * Generate preview
     */
    public function generatePreview(): array
    {
        $sampleData = $this->preview_data ?? $this->generateSampleData();

        return $this->renderContent($sampleData);
    }

    /**
     * Generate sample data for preview
     */
    protected function generateSampleData(): array
    {
        return [
            'user_name' => 'John Doe',
            'user_email' => 'john@example.com',
            'site_name' => 'QVEX Marketplace',
            'site_url' => config('app.url'),
            'support_email' => 'support@qvex.com',
            'current_date' => now()->format('M d, Y'),
            'vehicle_name' => '2023 Toyota Camry',
            'payment_amount' => '$25,000',
            'booking_id' => 'BK-12345',
        ];
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
            ->where('is_active', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeDefaults($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeTransactional($query)
    {
        return $query->where('category', self::CATEGORY_TRANSACTIONAL);
    }

    public function scopeMarketing($query)
    {
        return $query->where('category', self::CATEGORY_MARKETING);
    }

    public function scopePopular($query, int $minUsage = 10)
    {
        return $query->where('usage_count', '>=', $minUsage)
            ->orderByDesc('usage_count');
    }

    public function scopeRecentlyUsed($query, int $days = 30)
    {
        return $query->where('updated_at', '>=', now()->subDays($days))
            ->where('usage_count', '>', 0)
            ->orderByDesc('updated_at');
    }

    public function scopeSearchByName($query, string $search)
    {
        return $query->where('name', 'LIKE', "%{$search}%");
    }

    public function scopeNeedsUpdate($query)
    {
        return $query->where('updated_at', '<', now()->subMonths(6))
            ->where('usage_count', '>', 0);
    }
}
