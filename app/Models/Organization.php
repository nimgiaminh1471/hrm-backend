<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'subdomain',
        'logo',
        'email',
        'phone',
        'address',
        'website',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    public function positions()
    {
        return $this->hasMany(Position::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function careers()
    {
        return $this->hasMany(Career::class);
    }
} 