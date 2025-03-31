<?php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class TenantPersonalAccessToken extends PersonalAccessToken
{
    use BelongsToTenant;

    protected $table = 'personal_access_tokens';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'name',
        'token',
        'abilities',
        'last_used_at',
        'expires_at',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'abilities' => 'array',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the tokenable model that owns the token.
     */
    public function tokenable()
    {
        return $this->morphTo();
    }
} 