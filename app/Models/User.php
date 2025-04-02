<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\EmploymentStatus;
use App\Enums\Gender;
use App\Enums\MaritalStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'organization_id',
        'name',
        'email',
        'password',
        'employee_id',
        'status',
        'first_name',
        'last_name',
        'gender',
        'date_of_birth',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'department_id',
        'position_id',
        'team_id',
        'manager_id',
        'hire_date',
        'employment_status',
        'employment_type',
        'salary',
        'bank_name',
        'bank_account',
        'bank_branch',
        'tax_id',
        'social_security_number',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'date_of_birth',
        'marital_status',
        'nationality',
        'national_id',
        'passport_number',
        'passport_expiry',
        'phone_emergency',
        'address_emergency',
        'joining_date',
        'exit_date',
        'skills',
        'certifications',
        'education',
        'experience',
        'profile_photo',
        'is_active'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
            'passport_expiry' => 'date',
            'joining_date' => 'date',
            'exit_date' => 'date',
            'is_active' => 'boolean',
            'skills' => 'array',
            'certifications' => 'array',
            'education' => 'array',
            'experience' => 'array',
            'gender' => Gender::class,
            'marital_status' => MaritalStatus::class,
            'employment_status' => EmploymentStatus::class,
            'salary' => 'decimal:2',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function managedDepartments(): HasMany
    {
        return $this->hasMany(Department::class, 'head_id');
    }

    public function managedTeams(): HasMany
    {
        return $this->hasMany(Team::class, 'leader_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function subordinates(): HasMany
    {
        return $this->hasMany(User::class, 'manager_id');
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
