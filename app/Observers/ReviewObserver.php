<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Traits\BaseAuditObserver;

class ReviewObserver
{
    use BaseAuditObserver;

    /**
     * Custom audit tags for Review model
     */
    protected function getCustomAuditTags($model, string $event): array
    {
        $tags = [];

        // Add rating-based tags
        if ($model->rating) {
            $tags[] = 'rating_'.$model->rating;

            if ($model->rating <= 2) {
                $tags[] = 'negative_review';
            } elseif ($model->rating >= 4) {
                $tags[] = 'positive_review';
            } else {
                $tags[] = 'neutral_review';
            }
        }

        // Add status-based tags
        if ($model->status) {
            $tags[] = 'status_'.$model->status;
        }

        // Add verification tags
        if (isset($model->verified_purchase) && $model->verified_purchase) {
            $tags[] = 'verified_purchase';
        }

        // Add flagging tags
        if (isset($model->flagged_inappropriate) && $model->flagged_inappropriate) {
            $tags[] = 'flagged_content';
        }

        return $tags;
    }

    /**
     * Handle review created event
     */
    public function auditCreated($model, $oldValues, $newValues)
    {
        $this->logCustomEvent(
            $model,
            'review_submitted',
            [
                'reviewer_id' => $model->reviewer_id,
                'reviewee_id' => $model->reviewee_id,
                'rating' => $model->rating,
                'has_content' => ! empty($model->content),
                'content_length' => strlen($model->content ?? ''),
                'has_pros' => ! empty($model->pros),
                'has_cons' => ! empty($model->cons),
                'would_recommend' => $model->would_recommend,
                'verified_purchase' => $model->verified_purchase,
                'vehicle_id' => $model->vehicle_id,
                'vendor_id' => $model->vendor_id,
            ]
        );
    }

    /**
     * Handle review updated event
     */
    public function auditUpdated($model, $oldValues, $newValues)
    {
        // Track status changes
        if (isset($newValues['status']) && isset($oldValues['status']) &&
            $oldValues['status'] !== $newValues['status']) {
            $this->logCustomEvent(
                $model,
                'review_status_changed',
                [
                    'old_status' => $oldValues['status'],
                    'new_status' => $newValues['status'],
                    'changed_by' => auth()->id(),
                ]
            );

            // Log specific status events
            if ($newValues['status'] === 'approved') {
                $this->logCustomEvent($model, 'review_approved', [
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                ]);
            } elseif ($newValues['status'] === 'rejected') {
                $this->logCustomEvent($model, 'review_rejected', [
                    'rejected_by' => auth()->id(),
                    'rejected_at' => now(),
                ]);
            }
        }

        // Track rating changes
        if (isset($newValues['rating']) && isset($oldValues['rating']) &&
            $oldValues['rating'] !== $newValues['rating']) {
            $this->logCustomEvent(
                $model,
                'review_rating_changed',
                [
                    'old_rating' => $oldValues['rating'],
                    'new_rating' => $newValues['rating'],
                    'rating_change' => $newValues['rating'] - $oldValues['rating'],
                ]
            );
        }

        // Track flagging changes
        if (isset($newValues['flagged_inappropriate']) && isset($oldValues['flagged_inappropriate']) &&
            $oldValues['flagged_inappropriate'] !== $newValues['flagged_inappropriate']) {
            if ($newValues['flagged_inappropriate']) {
                $this->logCustomEvent(
                    $model,
                    'review_flagged',
                    [
                        'flagged_by' => auth()->id(),
                        'flagged_reason' => $model->flagged_reason,
                        'flagged_at' => now(),
                    ]
                );
            } else {
                $this->logCustomEvent(
                    $model,
                    'review_unflagged',
                    [
                        'unflagged_by' => auth()->id(),
                        'unflagged_at' => now(),
                    ]
                );
            }
        }

        // Track helpful count changes (if significant)
        if (isset($newValues['helpful_count']) && isset($oldValues['helpful_count'])) {
            $helpfulChange = $newValues['helpful_count'] - $oldValues['helpful_count'];
            if (abs($helpfulChange) >= 1) {
                $this->logCustomEvent(
                    $model,
                    'review_helpfulness_updated',
                    [
                        'helpful_change' => $helpfulChange,
                        'new_helpful_count' => $newValues['helpful_count'],
                        'new_not_helpful_count' => $newValues['not_helpful_count'] ?? $oldValues['not_helpful_count'] ?? 0,
                    ]
                );
            }
        }
    }

    /**
     * Handle review deleted event
     */
    public function auditDeleted($model, $oldValues, $newValues)
    {
        $this->logCustomEvent(
            $model,
            'review_deleted',
            [
                'deletion_type' => 'soft_delete',
                'deleted_by' => auth()->id(),
                'final_rating' => $oldValues['rating'] ?? null,
                'final_status' => $oldValues['status'] ?? null,
                'was_flagged' => $oldValues['flagged_inappropriate'] ?? false,
                'helpful_count' => $oldValues['helpful_count'] ?? 0,
            ]
        );
    }

    /**
     * Handle review restored event
     */
    public function auditRestored($model, $oldValues, $newValues)
    {
        $this->logCustomEvent(
            $model,
            'review_restored',
            [
                'restored_by' => auth()->id(),
                'restoration_reason' => request()->get('restoration_reason'),
                'current_status' => $model->status,
            ]
        );
    }

    /**
     * Log review helpful/not helpful votes
     */
    public function logHelpfulnessVote($review, bool $helpful, $userId = null)
    {
        $this->logCustomEvent(
            $review,
            $helpful ? 'review_marked_helpful' : 'review_marked_not_helpful',
            [
                'voter_id' => $userId ?? auth()->id(),
                'vote_type' => $helpful ? 'helpful' : 'not_helpful',
                'voted_at' => now(),
            ]
        );
    }

    /**
     * Log review response events
     */
    public function logReviewResponse($review, $responseData)
    {
        $this->logCustomEvent(
            $review,
            'review_response_added',
            [
                'responder_id' => auth()->id(),
                'response_type' => $responseData['response_type'] ?? 'general',
                'response_length' => isset($responseData['response_text']) ?
                    strlen($responseData['response_text']) : 0,
                'is_public' => $responseData['is_public'] ?? true,
                'responded_at' => now(),
            ]
        );
    }

    /**
     * Log review verification events
     */
    public function logReviewVerification($review, bool $verified)
    {
        $this->logCustomEvent(
            $review,
            $verified ? 'review_verified' : 'review_unverified',
            [
                'verified_by' => auth()->id(),
                'verification_method' => request()->get('verification_method', 'manual'),
                'verified_at' => now(),
            ]
        );
    }

    /**
     * Log review moderation events
     */
    public function logModerationAction($review, string $action, array $data = [])
    {
        $this->logCustomEvent(
            $review,
            'review_moderated',
            array_merge([
                'moderation_action' => $action,
                'moderator_id' => auth()->id(),
                'moderated_at' => now(),
            ], $data)
        );
    }

    /**
     * Should include request data for sensitive review operations
     */
    protected function shouldIncludeRequestData($model, string $event): bool
    {
        $sensitiveEvents = [
            AuditLog::EVENT_CREATED,
            'review_flagged',
            'review_approved',
            'review_rejected',
            'review_deleted',
            'review_moderated',
        ];

        return in_array($event, $sensitiveEvents);
    }

    /**
     * Get model-specific exclude fields
     */
    protected function getModelExcludeFields($model): array
    {
        // Get base exclude fields from the trait
        $baseExcludeFields = [];
        if (property_exists($model, 'auditExclude')) {
            $baseExcludeFields = $model->auditExclude;
        }

        return array_merge(
            $baseExcludeFields,
            [
                // Exclude potentially sensitive fields
                'flagged_reason', // This might contain sensitive information
            ]
        );
    }
}
