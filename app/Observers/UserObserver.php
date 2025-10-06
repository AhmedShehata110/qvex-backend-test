<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\User;
use App\Traits\BaseAuditObserver;
use Illuminate\Support\Facades\Auth;

class UserObserver
{
    use BaseAuditObserver;

    /**
     * Custom audit tags for User model
     */
    protected function getCustomAuditTags($model, string $event): array
    {
        $tags = [];

        // Add user type tags
        if ($model->user_type) {
            $tags[] = 'user_type_'.$model->user_type->value;

            // Add admin tag based on user type
            if ($model->user_type->isAdmin()) {
                $tags[] = 'admin_user';
            } else {
                $tags[] = 'regular_user';
            }
        }

        // Add status-based tags
        if (isset($model->is_active)) {
            $tags[] = $model->is_active ? 'active_user' : 'inactive_user';
        }

        // Add verification status tags
        if ($model->email_verified_at) {
            $tags[] = 'verified_user';
        } else {
            $tags[] = 'unverified_user';
        }

        return $tags;
    }

    /**
     * Handle user created event with special logging
     */
    public function auditCreated($model, $oldValues, $newValues)
    {
        // Log user registration
        $this->logCustomEvent(
            $model,
            'user_registered',
            [
                'registration_method' => request()->get('registration_method', 'web'),
                'email' => $model->email,
                'user_type' => $model->user_type?->value,
            ]
        );
    }

    /**
     * Handle user updated event with special password tracking
     */
    public function auditUpdated($model, $oldValues, $newValues)
    {
        // Check if password was changed
        if (isset($newValues['password']) && isset($oldValues['password']) &&
            $oldValues['password'] !== $newValues['password']) {
            $this->logCustomEvent(
                $model,
                AuditLog::EVENT_PASSWORD_CHANGED,
                [
                    'password_changed_at' => now(),
                    'ip_address' => request()->ip(),
                ]
            );
        }

        // Check if email was changed
        if (isset($newValues['email']) && isset($oldValues['email']) &&
            $oldValues['email'] !== $newValues['email']) {
            $this->logCustomEvent(
                $model,
                'email_changed',
                [
                    'old_email' => $oldValues['email'],
                    'new_email' => $newValues['email'],
                    'verification_reset' => true,
                ]
            );
        }

        // Check if user type was changed
        if (isset($newValues['user_type']) && isset($oldValues['user_type']) &&
            $oldValues['user_type'] !== $newValues['user_type']) {
            $this->logCustomEvent(
                $model,
                'user_type_changed',
                [
                    'old_user_type' => $oldValues['user_type']?->value ?? $oldValues['user_type'],
                    'new_user_type' => $newValues['user_type']?->value ?? $newValues['user_type'],
                    'changed_by' => Auth::id(),
                ]
            );
        }

        // Check if user was activated/deactivated
        if (isset($newValues['is_active']) && isset($oldValues['is_active']) &&
            $oldValues['is_active'] !== $newValues['is_active']) {
            $event = $newValues['is_active'] ? 'user_activated' : 'user_deactivated';
            $this->logCustomEvent(
                $model,
                $event,
                [
                    'status_changed_by' => Auth::id(),
                    'previous_status' => $oldValues['is_active'],
                ]
            );
        }
    }

    /**
     * Handle user deleted event
     */
    public function auditDeleted($model, $oldValues, $newValues)
    {
        $this->logCustomEvent(
            $model,
            'user_deactivated',
            [
                'deletion_type' => 'soft_delete',
                'deleted_by' => Auth::id(),
                'user_email' => $oldValues['email'] ?? null,
                'user_type' => $oldValues['user_type']?->value ?? $oldValues['user_type'] ?? null,
            ]
        );
    }

    /**
     * Handle user restored event
     */
    public function auditRestored($model, $oldValues, $newValues)
    {
        $this->logCustomEvent(
            $model,
            'user_restored',
            [
                'restored_by' => Auth::id(),
                'restoration_reason' => request()->get('restoration_reason'),
            ]
        );
    }

    /**
     * Log successful login
     */
    public function logLogin($user)
    {
        $this->logAuthEvent(
            $user,
            AuditLog::EVENT_LOGIN,
            [
                'login_method' => request()->get('login_method', 'web'),
                'user_agent' => request()->userAgent(),
                'ip_address' => request()->ip(),
                'login_time' => now(),
            ]
        );
    }

    /**
     * Log successful logout
     */
    public function logLogout($user)
    {
        $this->logAuthEvent(
            $user,
            AuditLog::EVENT_LOGOUT,
            [
                'logout_method' => request()->get('logout_method', 'web'),
                'session_duration' => session()->get('login_time') ?
                    now()->diffInMinutes(session()->get('login_time')) : null,
            ]
        );
    }

    /**
     * Log failed login attempt
     */
    public function logFailedLogin(string $email, string $reason = 'invalid_credentials')
    {
        // Try to find user by email for audit purposes
        $user = User::where('email', $email)->first();

        AuditLog::createEntry(
            $user ?? new User(['email' => $email]),
            'login_failed',
            null,
            [
                'email' => $email,
                'failure_reason' => $reason,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'attempted_at' => now(),
            ],
            [
                'tags' => 'authentication,login_failed,security',
                'include_request_data' => true,
            ]
        );
    }

    /**
     * Log email verification
     */
    public function logEmailVerification($user)
    {
        $this->logCustomEvent(
            $user,
            AuditLog::EVENT_EMAIL_VERIFIED,
            [
                'verified_at' => now(),
                'verification_method' => request()->get('verification_method', 'email_link'),
            ]
        );
    }

    /**
     * Log password reset request
     */
    public function logPasswordResetRequest($user)
    {
        $this->logCustomEvent(
            $user,
            'password_reset_requested',
            [
                'requested_at' => now(),
                'ip_address' => request()->ip(),
            ]
        );
    }

    /**
     * Log successful password reset
     */
    public function logPasswordReset($user)
    {
        $this->logCustomEvent(
            $user,
            'password_reset_completed',
            [
                'reset_at' => now(),
                'ip_address' => request()->ip(),
            ]
        );
    }

    /**
     * Should include request data for sensitive user operations
     */
    protected function shouldIncludeRequestData($model, string $event): bool
    {
        $sensitiveEvents = [
            AuditLog::EVENT_CREATED,
            AuditLog::EVENT_PASSWORD_CHANGED,
            'user_type_changed',
            'user_activated',
            'user_deactivated',
            AuditLog::EVENT_LOGIN,
        ];

        return in_array($event, $sensitiveEvents);
    }

    /**
     * Get model-specific exclude fields for user auditing
     */
    protected function getModelExcludeFields($model): array
    {
        return array_merge(
            $this->getDefaultExcludeFields(),
            [
                'password', // Never log actual password values
                'remember_token',
                'email_verification_token',
                'password_reset_token',
            ]
        );
    }
}
