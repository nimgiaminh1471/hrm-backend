<?php

namespace App\Models;

use App\Enums\CareerStatus;
use App\Enums\ContractType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Career extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'position_id',
        'department_id',
        'title',
        'description',
        'requirements',
        'responsibilities',
        'benefits',
        'location',
        'type',
        'number_of_positions',
        'application_deadline',
        'status',
        'is_featured'
    ];

    protected $casts = [
        'application_deadline' => 'date',
        'is_featured' => 'boolean',
        'type' => ContractType::class,
        'status' => CareerStatus::class,
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
} 