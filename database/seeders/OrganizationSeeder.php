<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\Organization;
use App\Models\OrganizationAssessment;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        $demo = Organization::create([
            'name' => 'PT Demo Perusahaan',
            'slug' => 'demo-perusahaan',
            'email' => 'hr@demo-perusahaan.test',
            'phone' => '02112345678',
            'address' => 'Jl. Sudirman No. 1, Jakarta',
            'industry' => 'Technology',
            'is_active' => true,
        ]);

        $acme = Organization::create([
            'name' => 'PT Acme Corp',
            'slug' => 'acme-corp',
            'email' => 'hr@acme.test',
            'phone' => '02187654321',
            'industry' => 'Manufacturing',
            'is_active' => true,
        ]);

        foreach (Assessment::all() as $assessment) {
            foreach ([$demo, $acme] as $org) {
                OrganizationAssessment::create([
                    'organization_id' => $org->id,
                    'assessment_id' => $assessment->id,
                    'is_enabled' => $assessment->is_active,
                    'cooldown_days' => $assessment->cooldown_days,
                ]);
            }
        }
    }
}
