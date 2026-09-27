<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailType extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable=['email_type_label'];


    public function sent_emails() {
        return $this->hasMany(SentEmail::class, 'email_type_id');
    }
}
