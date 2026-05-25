<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assessment extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'type',
        'description',
        'instructions',
        'question_count',
        'cooldown_days',
        'icon',
        'color',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'question_count' => 'integer',
            'cooldown_days' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function results(): HasMany
    {
        return $this->hasMany(AssessmentResult::class);
    }

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'organization_assessments')
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

    public function organizationAssessments(): HasMany
    {
        return $this->hasMany(OrganizationAssessment::class);
    }
}
