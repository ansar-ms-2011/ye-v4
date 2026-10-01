<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationLanguage extends Model
{
    use HasFactory;

    protected $appends = ['remove'];

    protected $fillable = [
        'application_no',
        'language',
        'years_studied',
        'speaking',
        'reading',
        'writing',
        'application_id',
    ];

    public function getRemoveAttribute()
    {
        return false;
    }
}
