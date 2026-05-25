<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RbacSeeder::class,
            AssessmentSeeder::class,
            OrganizationSeeder::class,
            SuperadminSeeder::class,
            AdminSeeder::class,
            CandidateSeeder::class,
        ]);
    }
}
