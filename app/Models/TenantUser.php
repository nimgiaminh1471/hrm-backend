<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\Contracts\HasApiTokens;
use Laravel\Sanctum\HasApiTokens as SanctumHasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class TenantUser extends Authenticatable implements HasApiTokens
{
    use Notifiable, SanctumHasApiTokens, HasRoles, BelongsToTenant;

    protected $table = 'users';
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
    ];

    /**
     * Get the personal access tokens for the user.
     */
    public function tokens(): MorphMany
    {
        return $this->morphMany(TenantPersonalAccessToken::class, 'tokenable');
    }
} 