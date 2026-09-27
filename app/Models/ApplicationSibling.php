<?php

namespace App\Models;

use App\Casts\BooleanToYesNo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationSibling extends Model
{
    use HasFactory;

    protected $casts=[
        'living_at_home'=>BooleanToYesNo::class,
    ];
protected $appends=['remove'];
    protected $fillable=[
        'application_id',
        'full_name',
        'gender',
        'age',
        'occupation',
        'living_at_home'
    ];

    public function application(){
        return $this->belongsTo(Application::class);
    }

    public function getRemoveAttribute()
    {
        return false;
    }
}
