<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'address',
        'industry',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function organizationAssessments(): HasMany
    {
        return $this->hasMany(OrganizationAssessment::class);
    }

    public function assessments(): BelongsToMany
    {
        return $this->belongsToMany(Assessment::class, 'organization_assessments')
            ->withPivot([
                'is_enabled',
                'cooldown_days',
                'custom_name',
                'custom_description',
                'custom_instructions',
                'settings',
            ])
            ->withTimestamps();
    }
}
