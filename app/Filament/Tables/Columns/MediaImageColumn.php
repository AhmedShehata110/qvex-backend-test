<?php

namespace App\Filament\Tables\Columns;

use Closure;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class MediaImageColumn extends ImageColumn
{
    protected string | Closure | null $collection = null;

    protected string | Closure | null $conversion = null;

    protected string | Closure | null $fallbackImageUrl = null;

    public function collection(string | Closure | null $collection): static
    {
        $this->collection = $collection;

        return $this;
    }

    public function conversion(string | Closure | null $conversion): static
    {
        $this->conversion = $conversion;

        return $this;
    }

    public function fallbackImageUrl(string | Closure | null $url): static
    {
        $this->fallbackImageUrl = $url;

        return $this;
    }

    public function getCollection(): ?string
    {
        return $this->evaluate($this->collection);
    }

    public function getConversion(): ?string
    {
        return $this->evaluate($this->conversion);
    }

    public function getFallbackImageUrl(): ?string
    {
        if ($this->fallbackImageUrl !== null) {
            $evaluated = $this->evaluate($this->fallbackImageUrl);
            return is_string($evaluated) ? $evaluated : null;
        }

        // Default to config value
        return config('media-library.fallback_image_url', asset('images/default-image.png'));
    }

    public function getImageUrl(?string $state = null): ?string
    {
        $record = $this->getRecord();

        if (! $record) {
            return $this->getFallbackImageUrl();
        }

        if ($this->hasRelationship($record)) {
            $record = $this->getRelationshipResults($record);
        }

        $records = Arr::wrap($record);

        $collection = $this->getCollection() ?? 'default';
        $conversion = $this->getConversion();

        foreach ($records as $record) {
            if (! $record instanceof Model) {
                continue;
            }

            // Check if model has media
            if (! method_exists($record, 'getFirstMediaUrl')) {
                continue;
            }

            $url = $record->getFirstMediaUrl($collection, $conversion ?? '');

            if (filled($url)) {
                return $url;
            }
        }

        // Return fallback image if no media found
        return $this->getFallbackImageUrl();
    }

    public function getState(): mixed
    {
        // Return the image URL directly as the state
        return $this->getImageUrl();
    }
}
