<?php

namespace App\Models;

use App\Casts\BooleanToYesNo;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Application extends Model
{
    protected $casts = [
        'parent_div_sep' => 'boolean',
        'parent1_rotarian' => BooleanToYesNo::class,
        'parent2_rotarian' => BooleanToYesNo::class,
        'dob' => 'date:d-m-Y',
        'date_of_app' => 'date',
    ];

    protected $fillable = [
        'idapplication',
        'media_id',
        'emergency_contact',
        'parent_div_sep',
        'dob',
        'gender',
        'email_address',
        'school',
        'why_rotary',
        'consider_alt_country',
        'how_did_you_hear',
        'firstname',
        'surname',
        'address',
        'city',
        'county',
        'postcode',
        'contact_no',
        'alt_contact_no',
        'parent_1',
        'parent_2',
        'application_no',
        'citizen_of',
        'other_info',
        'exchange_type',
        'pref_name',
        'parent_support',
        'dyeo_id',
        'realm',
        'application_status',
        'application_status_note',
        'dyeo_assigned',
        'application_fee',
        'application_fee_paid',
        'parent1_rotarian',
        'parent2_rotarian',
        'parent1_rotary_club',
        'parent2_rotary_club',
        'parent1_email',
        'parent2_email',
        'parent1_tel',
        'parent2_tel',
        'parent1_mobile',
        'parent2_mobile',
        'parent1_btel',
        'parent2_btel',
        'parent1_occupation',
        'parent2_occupation',
        'religion',
        'religion_detail',
        'diet_restriction',
        'smoke',
        'smoke_why',
        'drink',
        'drink_why',
        'illegal_drugs',
        'illegal_drugs_why',
        'native_language',
        'dietary_restriction',
        'medical_condition',
        'treated_condition',
        'prescribed_meds',
        'special_req',
        'medical_info',
        'rotary_club_id',
        'free_activities',
        'attainment_vocation',
        'special_interests',
        'special_skills',
        'contrib_entertainment',
        'reason_for_camp',
        'personal_remarks',
        'place_of_birth',
        'em_name',
        'em_relationship',
        'em_htel',
        'em_mobile',
        'em_email',
        'country_citizenship',
        'image_location',
        'date_of_app',
    ];

    protected $appends = ['image_data', 'full_name', 'application_season_class'];

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function club()
    {
        return $this->belongsTo(RibiClub::class, 'rotary_club_id');
    }

    public function dyeo()
    {
        return $this->belongsTo(RibiDyeo::class, 'dyeo_id');
    }

    public function address_home()
    {
        return $this->hasOne(ApplicationAddress::class)->where('address_type', 'HOME');
    }

    public function address_postal()
    {
        return $this->hasOne(ApplicationAddress::class)->where('address_type', 'POSTAL');
    }

    public function address_emergency()
    {
        return $this->hasOne(ApplicationAddress::class)->where('address_type', 'EMERGENCY');
    }

    public function address_parent1()
    {
        return $this->hasOne(ApplicationAddress::class)->where('address_type', 'PARENT1');
    }

    public function address_parent2()
    {
        return $this->hasOne(ApplicationAddress::class)->where('address_type', 'PARENT2');
    }

    public function languages(): HasMany
    {
        return $this->hasMany(ApplicationLanguage::class);
    }

    public function siblings(): HasMany
    {
        return $this->hasMany(ApplicationSibling::class);
    }

    public function media_library(): HasMany
    {
        return $this->hasMany(Media::class, 'application_id');
    }

    public function getGenderAttribute($value): string
    {
        return ($value === 1 || $value === '1' || $value === 'Female') ? 'Female' : 'Male';
    }

    public function getImageDataAttribute()
    {
        if ($this->media_id > 0) {
            $obj = Media::find($this->media_id);

            return $obj->media ?? '';
        }

        return '';
    }

    private function getEmptyAddress(): ApplicationAddress
    {
        $obj = new ApplicationAddress;
        $obj->id = '';
        $obj->application_no = '9999';
        $obj->address_type = '';
        $obj->street = '';
        $obj->city = '';
        $obj->county = '';
        $obj->country = '';
        $obj->postcode = '';
        $obj->application_id = '';

        return $obj;
    }

    public function getFullNameAttribute()
    {
        return strtoupper($this->surname).' '.$this->firstname;
    }

    public function getGuideNameAttribute()
    {
        return strtoupper($this->firstname.' '.$this->surname);
    }

    public function getApplicationSeasonClassAttribute()
    {
        return getApplicationClass(Carbon::parse($this->date_of_app));
    }

//    public function getDateOfAppAttribute($value)
//    {
//        return date('Y-m-d', strtotime($value));
//    }
}
