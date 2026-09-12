<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

#[Fillable(['wordpress_id', 'title', 'alt_text', 'file_path', 'original_url', 'mime_type', 'file_size', 'wordpress_parent_id', 'metadata'])]
class MediaAsset extends Model
{
    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }

    public function getPublicUrlAttribute(): ?string
    {
        return $this->file_path ? Storage::disk('public')->url($this->file_path) : $this->original_url;
    }
}
