<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDetail extends Model
{
    protected $fillable = [
        'user_id',
        'employee_id',
        'phone',
        'bio',
        'avatar_url',
        'language',
        'timezone',
        'birth_date',
        'gender',
        'company',
        'position',
        'department',
        'department_id',
        'position_id',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function departmentEntity(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function positionEntity(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    public function departmentLabel(): ?string
    {
        return $this->departmentEntity?->name ?? $this->department;
    }

    public function positionLabel(): ?string
    {
        return $this->positionEntity?->name ?? $this->position;
    }
}
