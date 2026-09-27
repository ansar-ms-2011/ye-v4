<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    const UPDATED_AT = null;
    protected $fillable = ['name',
        'media',
        'media_category_label',
        'brief_caption',
        'file_name',
        'media_category_id',
        'application_id',
        'id'
    ];
    protected $appends = ['changed'];
    public function getChangedAttribute()
    {
        return false;
    }
}
