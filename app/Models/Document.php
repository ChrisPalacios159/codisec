<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable(['wordpress_id', 'document_category_id', 'title', 'slug', 'description', 'file_path', 'external_url', 'mime_type', 'file_size', 'status', 'is_featured', 'published_at', 'metadata'])]
class Document extends Model
{
    protected function casts(): array
    {
        return ['published_at' => 'datetime', 'is_featured' => 'boolean', 'metadata' => 'array'];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(DocumentCategory::class, 'document_category_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')->where(fn (Builder $q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getPublicUrlAttribute(): ?string
    {
        return $this->file_path ? Storage::disk('public')->url($this->file_path) : $this->external_url;
    }
}
