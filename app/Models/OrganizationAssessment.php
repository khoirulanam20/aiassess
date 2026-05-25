<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganizationAssessment extends Model
{
    protected $fillable = [
        'organization_id',
        'assessment_id',
        'is_enabled',
        'cooldown_days',
        'custom_name',
        'custom_description',
        'custom_instructions',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'cooldown_days' => 'integer',
            'settings' => 'array',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function displayName(): string
    {
        return $this->custom_name ?? $this->assessment->name;
    }

    public function effectiveCooldownDays(): int
    {
        return $this->cooldown_days ?? $this->assessment->cooldown_days;
    }
}
