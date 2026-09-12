<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['wordpress_id', 'title', 'slug', 'excerpt', 'content', 'status', 'template', 'menu_order', 'is_featured', 'published_at', 'metadata'])]
class Page extends Model
{
    protected function casts(): array
    {
        return ['published_at' => 'datetime', 'is_featured' => 'boolean', 'metadata' => 'array'];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')->where(fn (Builder $q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
