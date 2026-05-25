<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperadminSeeder extends Seeder
{
    public function run(): void
    {
        $superadmin = User::create([
            'name' => 'Superadmin',
            'email' => 'superadmin@aiassess.test',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $superadmin->assignRole('superadmin');

        UserDetail::create([
            'user_id' => $superadmin->id,
            'language' => 'id',
        ]);
    }
}
