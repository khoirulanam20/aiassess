<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $organization = Organization::where('slug', 'demo-perusahaan')->first();

        $admin = User::create([
            'name' => 'Admin HR',
            'email' => 'admin@aiassess.test',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'organization_id' => $organization?->id,
        ]);

        $admin->assignRole('admin');

        UserDetail::create([
            'user_id' => $admin->id,
            'language' => 'id',
        ]);
    }
}
