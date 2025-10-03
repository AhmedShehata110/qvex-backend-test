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
 * Base Model Class
 *
 * Provides common functionality for all application models including:
 * - Automatic fillable merging with 'is_active' and 'added_by_id'
 * - Media management (images, files, videos) via Spatie MediaLibrary
 * - Multi-language support via Spatie Translatable
 * - Soft deletes and activation status
 * - Filterable queries
 *
 * @property bool $is_active
 * @property int|null $added_by_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
class BaseModel extends Model implements HasMedia
{
    use Filterable, HasActivation, HasTranslations, InteractsWithMedia, SoftDeletes;

    /**
     * Default images collection name
     * Can be overridden in child models
     */
    public string $imagesCollection = 'images';

    /**
     * Default media collections with their configuration
     * Override in child models to define custom collections
     * 
     * Format: 'collection_name' => [
     *     'mimes' => ['image/jpeg', 'image/png', ...],
     *     'single' => true/false (optional, default false)
     * ]
     * 
     * @var array<string, array>
     */
    protected array $customMediaCollections = [];

    /**
     * Base fillable attributes that will be merged with child model fillables
     *
     * @var array<string>
     */
    protected array $baseFillable = [
        'is_active',
        'added_by_id',
    ];

    /**
     * The attributes that should be cast
     *
     * @var array<string, string>
     */
    protected $casts = [
        'role' => UserTypeEnum::class,
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the fillable attributes for the model.
     * Automatically merges base fillable with child model fillable.
     *
     * @return array<string>
     */
    public function getFillable(): array
    {
        return array_merge($this->baseFillable, $this->fillable);
    }

    /**
     * Override the fill method to use merged fillable attributes
     *
     * @return $this
     */
    public function fill(array $attributes): static
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
     *
     * @param  string  $key
     */
    public function isFillable($key): bool
    {
        return in_array($key, $this->getFillable());
    }

    /**
     * Store images to media collection
     *
     * @param  mixed  $media  Single file, array of files, or collection
     * @param  bool  $update  If true, deletes existing media before adding new
     * @param  string  $collection  Collection name to store media
     */
    public function storeImages(mixed $media, bool $update = false, string $collection = 'images'): void
    {
        $images = array_filter(convertToArray($media));

        if ($update && ! empty($images)) {
            $this->deleteMedia($collection);
        }

        if (! empty($images)) {
            foreach ($images as $image) {
                if (method_exists($image, 'isValid') && $image->isValid()) {
                    $this->addMedia($image)->toMediaCollection($collection);
                }
            }
        }
    }

    /**
     * Store files to media collection
     *
     * @param  mixed  $media  Single file, array of files, or collection
     * @param  bool  $update  If true, deletes existing media before adding new
     * @param  string  $collection  Collection name to store media
     */
    public function storeFiles(mixed $media, bool $update = false, string $collection = 'files'): void
    {
        $files = array_filter(convertToArray($media));

        if ($update && ! empty($files)) {
            $this->deleteMedia($collection);
        }

        if (! empty($files)) {
            foreach ($files as $file) {
                if (method_exists($file, 'isValid') && $file->isValid()) {
                    $this->addMedia($file)->toMediaCollection($collection);
                }
            }
        }
    }

    /**
     * Store videos to media collection
     *
     * @param  mixed  $media  Single video, array of videos, or collection
     * @param  bool  $update  If true, deletes existing media before adding new
     * @param  string  $collection  Collection name to store media
     */
    public function storeVideos(mixed $media, bool $update = false, string $collection = 'videos'): void
    {
        $videos = array_filter(convertToArray($media));

        if ($update && ! empty($videos)) {
            $this->clearMediaCollection($collection);
        }

        if (! empty($videos)) {
            foreach ($videos as $video) {
                if (method_exists($video, 'isValid') && $video->isValid()) {
                    $this->addMedia($video)->toMediaCollection($collection);
                }
            }
        }
    }

    /**
     * Delete all media from a collection
     *
     * @param  string  $collection  Collection name
     */
    public function deleteMedia(string $collection = 'images'): void
    {
        $media = $this->getMedia($collection);

        foreach ($media as $mediaItem) {
            $mediaItem->delete();
        }
    }

    /**
     * Get all image URLs from a collection
     *
     * @param  string  $collection  Collection name
     * @return Collection<int, string>
     */
    public function getImages(string $collection = 'images'): Collection
    {
        return $this->getMedia($collection)->map(fn ($media) => $media->getUrl());
    }

    /**
     * Get first image URL from a collection
     *
     * @param  string  $collection  Collection name
     */
    public function getImage(string $collection = 'images'): string
    {
        return $this->getFirstMediaUrl($collection) ?: $this->getDefaultImageUrl();
    }

    /**
     * Get all video URLs from a collection
     *
     * @param  string  $collection  Collection name
     * @return Collection<int, string>
     */
    public function getVideos(string $collection = 'videos'): Collection
    {
        return $this->getMedia($collection)->map(fn ($media) => $media->getUrl());
    }

    /**
     * Get first video URL from a collection
     *
     * @param  string  $collection  Collection name
     */
    public function getVideo(string $collection = 'videos'): string
    {
        return $this->getFirstMediaUrl($collection) ?: $this->getDefaultImageUrl();
    }

    /**
     * Get all file URLs from a collection
     *
     * @param  string  $collection  Collection name
     * @return Collection<int, string>
     */
    public function getFiles(string $collection = 'files'): Collection
    {
        return $this->getMedia($collection)->map(fn ($media) => $media->getUrl());
    }

    /**
     * Get first file URL from a collection
     *
     * @param  string  $collection  Collection name
     */
    public function getFile(string $collection = 'files'): string
    {
        return $this->getFirstMediaUrl($collection) ?: $this->getDefaultImageUrl();
    }

    /**
     * Get image attribute accessor
     * Returns the first image URL from the default or custom collection
     */
    public function getImageAttribute(): string
    {
        return $this->getFirstMediaUrl($this->imagesCollection) ?: $this->getDefaultImageUrl();
    }

    /**
     * Register media collections
     * Automatically registers collections defined in $customMediaCollections property
     * with fallback URLs for empty collections
     */
    public function registerMediaCollections(): void
    {
        $defaultImageUrl = asset('defaults/default-image.png');
        $defaultImagePath = public_path('defaults/default-image.png');

        // Register default collections if $customMediaCollections is empty
        if (empty($this->customMediaCollections)) {
            $this->addMediaCollection($this->imagesCollection)
                ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'])
                ->useFallbackUrl($defaultImageUrl)
                ->useFallbackPath($defaultImagePath);

            $this->addMediaCollection('files')
                ->acceptsMimeTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                ->useFallbackUrl($defaultImageUrl)
                ->useFallbackPath($defaultImagePath);

            $this->addMediaCollection('videos')
                ->acceptsMimeTypes(['video/mp4', 'video/mpeg', 'video/quicktime', 'video/webm'])
                ->useFallbackUrl($defaultImageUrl)
                ->useFallbackPath($defaultImagePath);
        } else {
            // Register custom collections from $customMediaCollections property
            foreach ($this->customMediaCollections as $collectionName => $config) {
                $collection = $this->addMediaCollection($collectionName)
                    ->acceptsMimeTypes($config['mimes'] ?? ['image/*'])
                    ->useFallbackUrl($config['fallbackUrl'] ?? $defaultImageUrl)
                    ->useFallbackPath($config['fallbackPath'] ?? $defaultImagePath);

                if ($config['single'] ?? false) {
                    $collection->singleFile();
                }
            }
        }
    }

    /**
     * Register media conversions
     * Creates thumbnail and medium sized versions of images
     * Override this method in child models for custom conversions
     */
    public function registerMediaConversions(?\Spatie\MediaLibrary\MediaCollections\Models\Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->sharpen(10)
            ->nonQueued()
            ->performOnCollections($this->imagesCollection);

        $this->addMediaConversion('medium')
            ->width(600)
            ->height(600)
            ->sharpen(10)
            ->nonQueued()
            ->performOnCollections($this->imagesCollection);

        $this->addMediaConversion('large')
            ->width(1200)
            ->height(1200)
            ->sharpen(5)
            ->performOnCollections($this->imagesCollection);
    }

    /**
     * Get the default image URL for when no media is present
     * Can be overridden in child models for custom defaults
     */
    protected function getDefaultImageUrl(): string
    {
        return asset('defaults/default-image.png');
    }

    /**
     * Get the user who added/created this record
     */
    public function addedBy(): BelongsTo
    {
        $userModel = config('auth.providers.users.model', User::class);

        return $this->belongsTo($userModel, 'added_by_id');
    }

    /**
     * Scope a query to only include active records
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive records
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to filter by added by user
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAddedBy($query, int $userId)
    {
        return $query->where('added_by_id', $userId);
    }
}
