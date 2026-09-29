<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RibiCyeo extends Model
{
    protected $table = 'ribi_cyeo';

    protected $fillable = [
        'id',
        'cyeo_name',
        'cyeo_email',
        'cyeo_sig',
        'cyeo_address',
        'cyeo_city',
        'cyeo_state',
        'cyeo_postcode',
        'cyeo_country',
        'cyeo_htel',
        'cyeo_mobile',
        'cyeo_fax',
        'ribi_club_id',
        'application_no',
        'user_id',
    ];

    public function club()
    {
        return $this->belongsTo(RibiClub::class, 'ribi_club_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
