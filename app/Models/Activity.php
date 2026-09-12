<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Fillable(['wordpress_id', 'title', 'slug', 'summary', 'content', 'occurred_at', 'cover_image', 'gallery', 'status', 'published_at', 'metadata'])]
class Activity extends Model
{
    protected function casts(): array
    {
        return ['occurred_at' => 'date', 'published_at' => 'datetime', 'gallery' => 'array', 'metadata' => 'array'];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function getCoverImageUrlAttribute(): ?string
    {
        if (! $this->cover_image) {
            return null;
        }

        if (Str::startsWith($this->cover_image, ['http://', 'https://', '/'])) {
            return $this->cover_image;
        }

        return Storage::disk('public')->url($this->cover_image);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
