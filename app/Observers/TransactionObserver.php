<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Traits\BaseAuditObserver;

class TransactionObserver
{
    use BaseAuditObserver;

    /**
     * Custom audit tags for Transaction model
     */
    protected function getCustomAuditTags($model, string $event): array
    {
        $tags = [];

        // Add status-based tags
        if ($model->status) {
            $tags[] = 'status_'.$model->status;
        }

        // Add transaction type tags
        if ($model->type) {
            $tags[] = 'type_'.$model->type;
        }

        // Add payment method tags
        if ($model->payment_method) {
            $tags[] = 'payment_'.$model->payment_method;
        }

        // Add amount range tags
        if ($model->amount) {
            if ($model->amount >= 100000) {
                $tags[] = 'high_value';
            } elseif ($model->amount >= 50000) {
                $tags[] = 'medium_value';
            } else {
                $tags[] = 'low_value';
            }
        }

        return $tags;
    }

    /**
     * Handle transaction created event
     */
    public function auditCreated($model, $oldValues, $newValues)
    {
        $this->logCustomEvent(
            $model,
            'transaction_initiated',
            [
                'buyer_id' => $model->buyer_id,
                'seller_id' => $model->seller_id,
                'vehicle_id' => $model->vehicle_id,
                'vendor_id' => $model->vendor_id ?? null,
                'amount' => $model->amount,
                'currency' => $model->currency,
                'type' => $model->type,
                'payment_method' => $model->payment_method,
                'initial_status' => $model->status,
            ]
        );
    }

    /**
     * Handle transaction updated event
     */
    public function auditUpdated($model, $oldValues, $newValues)
    {
        // Track status changes
        if (isset($newValues['status']) && isset($oldValues['status']) &&
            $oldValues['status'] !== $newValues['status']) {
            $this->logCustomEvent(
                $model,
                'transaction_status_changed',
                [
                    'old_status' => $oldValues['status'],
                    'new_status' => $newValues['status'],
                    'changed_by' => auth()->id(),
                    'status_change_reason' => request()->get('status_change_reason'),
                ]
            );

            // Log specific status events
            $this->logSpecificStatusEvent($model, $newValues['status'], $oldValues['status']);
        }

        // Track amount changes
        if (isset($newValues['amount']) && isset($oldValues['amount']) &&
            $oldValues['amount'] != $newValues['amount']) {
            $this->logCustomEvent(
                $model,
                'transaction_amount_changed',
                [
                    'old_amount' => $oldValues['amount'],
                    'new_amount' => $newValues['amount'],
                    'amount_change' => $newValues['amount'] - $oldValues['amount'],
                    'changed_by' => auth()->id(),
                ]
            );
        }

        // Track payment method changes
        if (isset($newValues['payment_method']) && isset($oldValues['payment_method']) &&
            $oldValues['payment_method'] !== $newValues['payment_method']) {
            $this->logCustomEvent(
                $model,
                'payment_method_changed',
                [
                    'old_payment_method' => $oldValues['payment_method'],
                    'new_payment_method' => $newValues['payment_method'],
                    'changed_by' => auth()->id(),
                ]
            );
        }

        // Track payment reference updates
        if (isset($newValues['payment_reference']) &&
            (! isset($oldValues['payment_reference']) ||
             $oldValues['payment_reference'] !== $newValues['payment_reference'])) {
            $this->logCustomEvent(
                $model,
                'payment_reference_updated',
                [
                    'has_previous_reference' => ! empty($oldValues['payment_reference']),
                    'updated_by' => auth()->id(),
                ]
            );
        }
    }

    /**
     * Log specific status events
     */
    protected function logSpecificStatusEvent($model, string $newStatus, string $oldStatus)
    {
        switch ($newStatus) {
            case 'pending_payment':
                $this->logCustomEvent($model, 'payment_pending', [
                    'pending_since' => now(),
                    'payment_deadline' => request()->get('payment_deadline'),
                ]);
                break;

            case 'paid':
                $this->logCustomEvent($model, 'payment_completed', [
                    'paid_at' => now(),
                    'payment_confirmation' => request()->get('payment_confirmation'),
                ]);
                break;

            case 'completed':
                $this->logCustomEvent($model, 'transaction_completed', [
                    'completed_at' => now(),
                    'completion_method' => request()->get('completion_method', 'manual'),
                    'transaction_duration_days' => $model->created_at ?
                        now()->diffInDays($model->created_at) : null,
                ]);
                break;

            case 'cancelled':
                $this->logCustomEvent($model, 'transaction_cancelled', [
                    'cancelled_at' => now(),
                    'cancelled_by' => auth()->id(),
                    'cancellation_reason' => request()->get('cancellation_reason'),
                    'previous_status' => $oldStatus,
                ]);
                break;

            case 'refunded':
                $this->logCustomEvent($model, 'transaction_refunded', [
                    'refunded_at' => now(),
                    'refund_amount' => request()->get('refund_amount', $model->amount),
                    'refund_reason' => request()->get('refund_reason'),
                ]);
                break;

            case 'disputed':
                $this->logCustomEvent($model, 'transaction_disputed', [
                    'disputed_at' => now(),
                    'dispute_reason' => request()->get('dispute_reason'),
                    'disputed_by_role' => $this->getUserRoleInTransaction($model),
                ]);
                break;

            case 'failed':
                $this->logCustomEvent($model, 'transaction_failed', [
                    'failed_at' => now(),
                    'failure_reason' => request()->get('failure_reason'),
                    'retry_possible' => request()->get('retry_possible', false),
                ]);
                break;
        }
    }

    /**
     * Get the current user's role in the transaction
     */
    protected function getUserRoleInTransaction($model): ?string
    {
        $userId = auth()->id();

        if ($userId === $model->buyer_id) {
            return 'buyer';
        } elseif ($userId === $model->seller_id) {
            return 'seller';
        } elseif ($model->vendor_id && $userId === $model->vendor?->user_id) {
            return 'vendor';
        }

        return 'admin';
    }

    /**
     * Log payment attempt events
     */
    public function logPaymentAttempt($transaction, array $paymentData)
    {
        $this->logCustomEvent(
            $transaction,
            'payment_attempted',
            [
                'payment_method' => $paymentData['method'] ?? null,
                'payment_processor' => $paymentData['processor'] ?? null,
                'amount' => $paymentData['amount'] ?? $transaction->amount,
                'currency' => $paymentData['currency'] ?? $transaction->currency,
                'attempt_timestamp' => now(),
                'payment_gateway_response' => $paymentData['gateway_response'] ?? null,
            ]
        );
    }

    /**
     * Log payment failure events
     */
    public function logPaymentFailure($transaction, string $reason, array $details = [])
    {
        $this->logCustomEvent(
            $transaction,
            'payment_failed',
            array_merge([
                'failure_reason' => $reason,
                'failed_at' => now(),
                'retry_count' => $details['retry_count'] ?? 0,
                'error_code' => $details['error_code'] ?? null,
            ], $details)
        );
    }

    /**
     * Log dispute resolution events
     */
    public function logDisputeResolution($transaction, string $resolution, array $details = [])
    {
        $this->logCustomEvent(
            $transaction,
            'dispute_resolved',
            array_merge([
                'resolution_type' => $resolution,
                'resolved_by' => auth()->id(),
                'resolved_at' => now(),
                'resolution_details' => $details['notes'] ?? null,
            ], $details)
        );
    }

    /**
     * Log commission calculation events
     */
    public function logCommissionCalculated($transaction, array $commissionData)
    {
        $this->logCustomEvent(
            $transaction,
            'commission_calculated',
            [
                'commission_amount' => $commissionData['amount'] ?? null,
                'commission_rate' => $commissionData['rate'] ?? null,
                'platform_fee' => $commissionData['platform_fee'] ?? null,
                'vendor_commission' => $commissionData['vendor_commission'] ?? null,
                'calculated_at' => now(),
            ]
        );
    }

    /**
     * Should include request data for financial transactions
     */
    protected function shouldIncludeRequestData($model, string $event): bool
    {
        $financialEvents = [
            AuditLog::EVENT_CREATED,
            'payment_completed',
            'transaction_cancelled',
            'transaction_refunded',
            'transaction_disputed',
            'payment_failed',
            AuditLog::EVENT_DELETED,
        ];

        return in_array($event, $financialEvents);
    }

    /**
     * Get model-specific exclude fields
     */
    protected function getModelExcludeFields($model): array
    {
        return array_merge(
            $this->getDefaultExcludeFields(),
            [
                // Exclude sensitive payment information
                'payment_token',
                'card_last_four',
                'payment_gateway_response', // Might contain sensitive data
            ]
        );
    }
}
