<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CandidateSeeder extends Seeder
{
    public function run(): void
    {
        $organization = Organization::where('slug', 'demo-perusahaan')->first();

        $candidate = User::create([
            'name' => 'Kandidat Demo',
            'email' => 'kandidat@aiassess.test',
            'password' => Hash::make(Str::random(32)),
            'email_verified_at' => now(),
            'organization_id' => $organization?->id,
        ]);

        $candidate->assignRole('candidate');

        UserDetail::create([
            'user_id' => $candidate->id,
            'language' => 'id',
            'phone' => '081234567890',
            'employee_id' => 'EMP-001',
            'position' => 'Software Engineer',
            'department' => 'Engineering',
        ]);

        User::factory(10)->create(['organization_id' => $organization?->id])->each(function ($user) {
            UserDetail::create([
                'user_id' => $user->id,
                'language' => 'id',
            ]);
            $user->assignRole('candidate');
        });
    }
}
