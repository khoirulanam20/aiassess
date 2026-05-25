<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Organization;
use App\Models\Position;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $organization = Organization::where('slug', 'demo-perusahaan')->first();

        if (! $organization) {
            return;
        }

        $engineering = Department::create([
            'organization_id' => $organization->id,
            'name' => 'Engineering',
            'code' => 'ENG',
            'is_active' => true,
        ]);

        $hr = Department::create([
            'organization_id' => $organization->id,
            'name' => 'Human Resources',
            'code' => 'HR',
            'is_active' => true,
        ]);

        $softwareEngineer = Position::create([
            'organization_id' => $organization->id,
            'department_id' => $engineering->id,
            'name' => 'Software Engineer',
            'code' => 'SE',
            'is_active' => true,
        ]);

        Position::create([
            'organization_id' => $organization->id,
            'department_id' => $hr->id,
            'name' => 'HR Specialist',
            'code' => 'HRS',
            'is_active' => true,
        ]);

        $candidate = User::where('email', 'kandidat@aiassess.test')->first();

        if ($candidate?->userDetail) {
            $candidate->userDetail->update([
                'department_id' => $engineering->id,
                'department' => $engineering->name,
                'position_id' => $softwareEngineer->id,
                'position' => $softwareEngineer->name,
            ]);
        }
    }
}
