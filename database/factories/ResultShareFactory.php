<?php

namespace Database\Factories;

use App\Models\ResultShare;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ResultShareFactory extends Factory
{
    protected $model = ResultShare::class;

    public function definition(): array
    {
        return [
            'assessment_result_id' => 1,
            'created_by' => 1,
            'share_token' => Str::random(48),
            'access_code_hash' => Hash::make('123456', ['cost' => 4]),
            'is_active' => true,
            'view_count' => 0,
            'failed_attempts' => 0,
        ];
    }

    public function locked(): static
    {
        return $this->state(fn (array $attrs) => [
            'locked_until' => now()->addMinutes(30),
            'failed_attempts' => 5,
        ]);
    }

    public function revoked(): static
    {
        return $this->state(fn (array $attrs) => [
            'is_active' => false,
            'revoked_at' => now(),
        ]);
    }
}
