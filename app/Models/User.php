<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Laravel\Sanctum\Contracts\HasApiTokens;
use Laravel\Sanctum\HasApiTokens as SanctumHasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasApiTokens
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SanctumHasApiTokens, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'name',
        'email',
        'password',
        'phone',
        'address',
        'avatar',
        'settings',
        'is_active',
        'is_central_user',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'settings' => 'array',
        'is_active' => 'boolean',
        'is_central_user' => 'boolean',
    ];

    /**
     * Get the company that owns the user.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the personal access tokens for the user.
     */
    public function tokens(): MorphMany
    {
        return $this->morphMany(PersonalAccessToken::class, 'tokenable');
    }

    /**
     * Check if the user is a central user.
     */
    public function isCentralUser(): bool
    {
        return $this->is_central_user;
    }

    /**
     * Check if the user is a company user.
     */
    public function isCompanyUser(): bool
    {
        return !$this->is_central_user;
    }
}
