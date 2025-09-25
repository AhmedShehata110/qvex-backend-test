<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Traits\BaseAuditObserver;

class VehicleObserver
{
    use BaseAuditObserver;

    /**
     * Custom audit tags for Vehicle model
     */
    protected function getCustomAuditTags($model, string $event): array
    {
        $tags = [];

        // Add status-based tags
        if ($model->status) {
            $tags[] = 'status_'.$model->status;
        }

        // Add condition-based tags
        if ($model->condition) {
            $tags[] = 'condition_'.$model->condition;
        }

        // Add listing type tags
        if ($model->listing_type) {
            $tags[] = 'type_'.$model->listing_type;
        }

        // Add featured/premium tags
        if (isset($model->is_featured) && $model->is_featured) {
            $tags[] = 'featured_listing';
        }

        if (isset($model->is_premium) && $model->is_premium) {
            $tags[] = 'premium_listing';
        }

        return $tags;
    }

    /**
     * Handle vehicle created event
     */
    public function auditCreated($model, $oldValues, $newValues)
    {
        $this->logCustomEvent(
            $model,
            'vehicle_listed',
            [
                'owner_id' => $model->user_id,
                'vendor_id' => $model->vendor_id ?? null,
                'make' => $model->make,
                'model_name' => $model->model,
                'year' => $model->year,
                'price' => $model->price,
                'listing_type' => $model->listing_type,
                'location' => $model->location,
            ]
        );
    }

    /**
     * Handle vehicle updated event
     */
    public function auditUpdated($model, $oldValues, $newValues)
    {
        // Track price changes
        if (isset($newValues['price']) && isset($oldValues['price']) &&
            $oldValues['price'] != $newValues['price']) {
            $this->logCustomEvent(
                $model,
                'price_changed',
                [
                    'old_price' => $oldValues['price'],
                    'new_price' => $newValues['price'],
                    'price_change' => $newValues['price'] - $oldValues['price'],
                    'percentage_change' => round((($newValues['price'] - $oldValues['price']) / $oldValues['price']) * 100, 2),
                ]
            );
        }

        // Track status changes
        if (isset($newValues['status']) && isset($oldValues['status']) &&
            $oldValues['status'] !== $newValues['status']) {
            $this->logCustomEvent(
                $model,
                'status_changed',
                [
                    'old_status' => $oldValues['status'],
                    'new_status' => $newValues['status'],
                    'changed_by' => auth()->id(),
                ]
            );

            // Log specific status events
            if ($newValues['status'] === 'sold') {
                $this->logCustomEvent($model, 'vehicle_sold', [
                    'sold_at' => now(),
                    'final_price' => $model->price,
                ]);
            } elseif ($newValues['status'] === 'reserved') {
                $this->logCustomEvent($model, 'vehicle_reserved', [
                    'reserved_at' => now(),
                ]);
            } elseif ($newValues['status'] === 'available' && $oldValues['status'] === 'reserved') {
                $this->logCustomEvent($model, 'reservation_cancelled', [
                    'cancelled_at' => now(),
                ]);
            }
        }

        // Track featured status changes
        if (isset($newValues['is_featured']) && isset($oldValues['is_featured']) &&
            $oldValues['is_featured'] !== $newValues['is_featured']) {
            $event = $newValues['is_featured'] ? 'featured_enabled' : 'featured_disabled';
            $this->logCustomEvent(
                $model,
                $event,
                [
                    'changed_by' => auth()->id(),
                    'feature_change_date' => now(),
                ]
            );
        }

        // Track mileage updates
        if (isset($newValues['mileage']) && isset($oldValues['mileage']) &&
            $oldValues['mileage'] != $newValues['mileage']) {
            $this->logCustomEvent(
                $model,
                'mileage_updated',
                [
                    'old_mileage' => $oldValues['mileage'],
                    'new_mileage' => $newValues['mileage'],
                    'mileage_difference' => $newValues['mileage'] - $oldValues['mileage'],
                ]
            );
        }

        // Track location changes
        if ((isset($newValues['location']) && isset($oldValues['location']) &&
            $oldValues['location'] !== $newValues['location']) ||
            (isset($newValues['latitude']) && isset($oldValues['latitude']) &&
            $oldValues['latitude'] != $newValues['latitude']) ||
            (isset($newValues['longitude']) && isset($oldValues['longitude']) &&
            $oldValues['longitude'] != $newValues['longitude'])) {
            $this->logCustomEvent(
                $model,
                'location_changed',
                [
                    'old_location' => $oldValues['location'] ?? null,
                    'new_location' => $newValues['location'] ?? null,
                    'coordinate_changed' => (
                        (isset($newValues['latitude']) && $oldValues['latitude'] != $newValues['latitude']) ||
                        (isset($newValues['longitude']) && $oldValues['longitude'] != $newValues['longitude'])
                    ),
                ]
            );
        }
    }

    /**
     * Handle vehicle deleted event
     */
    public function auditDeleted($model, $oldValues, $newValues)
    {
        $this->logCustomEvent(
            $model,
            'vehicle_delisted',
            [
                'deletion_type' => 'soft_delete',
                'deleted_by' => auth()->id(),
                'final_status' => $oldValues['status'] ?? null,
                'final_price' => $oldValues['price'] ?? null,
                'listing_duration_days' => $model->created_at ?
                    now()->diffInDays($model->created_at) : null,
            ]
        );
    }

    /**
     * Handle vehicle restored event
     */
    public function auditRestored($model, $oldValues, $newValues)
    {
        $this->logCustomEvent(
            $model,
            'vehicle_relisted',
            [
                'restored_by' => auth()->id(),
                'restoration_reason' => request()->get('restoration_reason'),
                'current_status' => $model->status,
            ]
        );
    }

    /**
     * Log vehicle view/interest events
     */
    public function logVehicleView($vehicle, $userId = null)
    {
        $this->logCustomEvent(
            $vehicle,
            'vehicle_viewed',
            [
                'viewer_id' => $userId,
                'viewer_type' => $userId ? 'authenticated' : 'guest',
                'viewed_at' => now(),
                'referrer' => request()->headers->get('referer'),
            ]
        );
    }

    /**
     * Log vehicle inquiry events
     */
    public function logVehicleInquiry($vehicle, $inquiryData)
    {
        $this->logCustomEvent(
            $vehicle,
            'inquiry_received',
            [
                'inquirer_id' => auth()->id(),
                'inquiry_type' => $inquiryData['type'] ?? 'general',
                'message_length' => isset($inquiryData['message']) ? strlen($inquiryData['message']) : 0,
                'contact_method' => $inquiryData['contact_method'] ?? 'email',
                'inquiry_date' => now(),
            ]
        );
    }

    /**
     * Log vehicle favorite events
     */
    public function logVehicleFavorited($vehicle, $userId)
    {
        $this->logCustomEvent(
            $vehicle,
            'vehicle_favorited',
            [
                'user_id' => $userId,
                'favorited_at' => now(),
            ]
        );
    }

    /**
     * Log vehicle unfavorited events
     */
    public function logVehicleUnfavorited($vehicle, $userId)
    {
        $this->logCustomEvent(
            $vehicle,
            'vehicle_unfavorited',
            [
                'user_id' => $userId,
                'unfavorited_at' => now(),
            ]
        );
    }

    /**
     * Log vehicle photo upload events
     */
    public function logPhotoUploaded($vehicle, $photoData)
    {
        $this->logCustomEvent(
            $vehicle,
            'photo_uploaded',
            [
                'photo_count' => count($vehicle->images ?? []),
                'photo_type' => $photoData['type'] ?? 'standard',
                'file_size' => $photoData['size'] ?? null,
                'uploaded_by' => auth()->id(),
            ]
        );
    }

    /**
     * Should include request data for important vehicle operations
     */
    protected function shouldIncludeRequestData($model, string $event): bool
    {
        $importantEvents = [
            AuditLog::EVENT_CREATED,
            'price_changed',
            'status_changed',
            'vehicle_sold',
            'featured_enabled',
            AuditLog::EVENT_DELETED,
        ];

        return in_array($event, $importantEvents);
    }

    /**
     * Get model-specific exclude fields
     */
    protected function getModelExcludeFields($model): array
    {
        return array_merge(
            $this->getDefaultExcludeFields(),
            [
                // Exclude potentially sensitive internal fields
                'admin_notes',
                'internal_reference',
            ]
        );
    }
}
