<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssessmentResult extends Model
{
    protected $fillable = [
        'user_id',
        'assessment_id',
        'test_name',
        'result',
        'scores',
        'answers',
        'stt',
        'transkrip',
        'status',
        'share_token',
        'share_views',
    ];

    protected function casts(): array
    {
        return [
            'share_views' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function shares(): HasMany
    {
        return $this->hasMany(ResultShare::class);
    }
}
