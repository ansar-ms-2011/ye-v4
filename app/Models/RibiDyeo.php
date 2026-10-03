<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RibiDyeo extends Model
{
    use HasFactory;

    protected $table = 'ribi_dyeo';

    protected $fillable = [
        'district_code',
        'dyeo_name',
        'dyeo_email',
        'dyeo_contact_no',
        'dyeo_address',
        'dyeo_city',
        'dyeo_state',
        'dyeo_postcode',
        'dyeo_country',
        'dyeo_htel',
        'dyeo_wtel',
        'dyeo_mobile',
        'dyeo_fax',
        'user_id',
        'full_name',
        'email',
        'password',
        'active',
        'district',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
