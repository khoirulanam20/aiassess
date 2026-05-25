<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentBatchShare extends Model
{
    protected $fillable = [
        'assessment_batch_id',
        'user_id',
        'created_by',
        'access_code_hash',
        'is_active',
        'view_count',
        'failed_attempts',
        'locked_until',
        'revoked_at',
        'revoked_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'view_count' => 'integer',
            'failed_attempts' => 'integer',
            'locked_until' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(AssessmentBatch::class, 'assessment_batch_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isValid(): bool
    {
        return $this->is_active
            && $this->revoked_at === null
            && ($this->batch?->status === AssessmentBatch::STATUS_ACTIVE);
    }

    public function isLocked(): bool
    {
        return $this->locked_until !== null && $this->locked_until->isFuture();
    }
}
