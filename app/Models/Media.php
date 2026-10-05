<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'id',
        'name',
        'media',
        'media_category_label',
        'brief_caption',
        'file_name',
        'media_category_id',
        'application_id',
    ];

    protected $appends = ['changed', 'media_url'];

    public function getChangedAttribute()
    {
        return false;
    }

    public function getMediaUrlAttribute(): ?string
    {
        if (empty($this->media_path)) {
            return null;
        }

        // If a full URL was already stored, return as-is
        if (str_starts_with($this->media_path, 'http://') || str_starts_with($this->media_path, 'https://')) {
            return $this->media_path;
        }

        return Storage::disk('public')->url($this->media_path);
    }
}
