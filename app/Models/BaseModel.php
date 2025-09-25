<?php

namespace App\Models;

use App\Enums\User\UserTypeEnum;
use App\Http\Filters\Filterable;
use App\Traits\HasActivation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

/**
 * The attributes that are mass assignable from BaseModel.
 * These will be automatically merged with child model fillables.
 */
class BaseModel extends Model implements HasMedia
{
    use Filterable, HasActivation, HasTranslations, InteractsWithMedia, SoftDeletes;

    protected $baseFillable = [
        'is_active',
        'added_by_id',
    ];

    protected $casts = [
        'role' => UserTypeEnum::class, // Ensure role is cast as enum
        'is_active' => 'boolean',
    ];

    /**
     * Get the fillable attributes for the model.
     * Automatically merges base fillable with child model fillable.
     */
    public function getFillable()
    {
        return array_merge($this->baseFillable, $this->fillable);
    }

    /**
     * Override the fill method to use merged fillable attributes
     */
    public function fill(array $attributes)
    {
        $totalFillable = $this->getFillable();

        foreach ($totalFillable as $key) {
            if (array_key_exists($key, $attributes)) {
                $this->setAttribute($key, $attributes[$key]);
            }
        }

        return $this;
    }

    /**
     * Determine if the given attribute may be mass assigned.
     */
    public function isFillable($key)
    {
        return in_array($key, $this->getFillable());
    }

    public function storeImages($media, $update = false, $collection = 'images'): void
    {
        $images = array_filter(convertToArray($media));
        if ($update && ! empty($images)) {
            $this->deleteMedia(collection: $collection);
        }
        if (! empty($images)) {
            foreach ($images as $image) {
                if ($image->isValid()) {
                    $this->addMedia($image)->toMediaCollection($collection);
                }
            }
        }
    }

    public function storeFiles($media, $update = false, $collection = 'files'): void
    {
        $files = array_filter(convertToArray($media));

        if ($update && ! empty($files)) {
            $this->deleteMedia(collection: $collection);
        }

        if (! empty($files)) {
            foreach ($files as $file) {
                if ($file->isValid()) {
                    $this->addMedia($file)->toMediaCollection($collection);
                }
            }
        }
    }

    public function deleteMedia($collection = 'images'): void
    {
        $media = $this->getMedia($collection);
        foreach ($media as $m) {
            $m->delete();
        }
    }

    public function storeVideos($media, $update = false, $collection = 'videos'): void
    {
        $videos = array_filter(convertToArray($media));
        if ($update && ! empty($videos)) {
            $this->clearMediaCollection($collection);
        }
        if (count($videos) > 0) {
            foreach ($videos as $video) {
                $this->addMedia($video)->toMediaCollection($collection);
            }
        }
    }

    public function deleteMediaVideos($collection = 'videos'): void
    {
        $media = $this->getMedia($collection);
        foreach ($media as $m) {
            $m->delete();
        }
    }

    public function getImages($collection = 'images'): array|Collection
    {
        return $this->getMedia($collection)->map(fn ($media) => $media->getUrl());
    }

    public function getImage($collection = 'images'): ?string
    {
        return $this->getFirstMediaUrl($collection) ?: null;
    }

    public function getVideos($collection = 'videos'): array|Collection
    {
        return $this->getMedia($collection)->map(fn ($media) => $media->getUrl());
    }

    public function getImageAttribute(): ?string
    {
        return $this->getFirstMediaUrl($this->imageCollectionName ?? 'images');
    }

    public function getFile($collection = 'files'): ?string
    {
        return $this->getFirstMediaUrl($collection) ?: null;
    }

    public function getVideo($collection = 'videos'): ?string
    {
        return $this->getFirstMediaUrl($collection) ?: null;
    }

    public function AddedBy(): BelongsTo
    {
        $userModel = config('auth.providers.users.model') ?? 'App\\Models\\User';

        return $this->belongsTo($userModel, 'added_by_id');
    }
}
