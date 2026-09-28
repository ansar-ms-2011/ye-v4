<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['full_name', 'email', 'password', 'application_id', 'district', 'rotary_club_id', 'active'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
            'password' => 'hashed',
            'active' => 'boolean',
        ];
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(RibiClub::class, 'rotary_club_id');
    }

    public function cyeo(): HasOne
    {
        return $this->hasOne(RibiCyeo::class);
    }

    public function dyeo(): HasOne
    {
        return $this->hasOne(RibiDyeo::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isDyeo(): bool
    {
        return $this->hasRole('dyeo');
    }

    public function isCyeo(): bool
    {
        return $this->hasRole('cyeo');
    }

    public function isApplicant(): bool
    {
        return $this->hasRole('applicant');
    }
}
