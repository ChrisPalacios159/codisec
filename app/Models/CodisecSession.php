<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['wordpress_id', 'title', 'slug', 'session_date', 'description', 'document_path', 'external_url', 'status', 'metadata'])]
class CodisecSession extends Model
{
    protected function casts(): array
    {
        return ['session_date' => 'date', 'metadata' => 'array'];
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
