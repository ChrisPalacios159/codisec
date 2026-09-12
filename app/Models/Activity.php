<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

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

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
