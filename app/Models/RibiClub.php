<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RibiClub extends Model
{
    use HasFactory;

    protected $table='ribi_clubs';

    protected $fillable = [
        'id',
        'club_name',
        'district_code',
        'district_id',
        'club_president',
        'club_president_email',
        'club_president_mobile',
        'club_president_sig',
        'club_other_name',
        'club_other_sig',
    ];

    public function cyeo()
    {
        return $this->hasOne(RibiCyeo::class, 'ribi_club_id');
    }
}
