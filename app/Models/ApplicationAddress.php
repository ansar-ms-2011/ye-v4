<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['id','application_no','address_type', 'street', 'city', 'county', 'postcode', 'country', 'application_id'])]
class ApplicationAddress extends Model
{
    //
}
