<?php

namespace Tests;

use App\Models\Organization;
use App\Models\User;
use Database\Seeders\OrganizationSeeder;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function seedRbac(): void
    {
        $this->seed(RbacSeeder::class);
        $this->seed(OrganizationSeeder::class);
    }

    protected function createUserWithRole(string $role = 'admin', array $attributes = []): User
    {
        $this->seedRbac();

        if (! isset($attributes['organization_id']) && $role !== 'superadmin') {
            $attributes['organization_id'] = Organization::first()?->id;
        }

        $user = User::factory()->create($attributes);
        $user->assignRole($role);

        return $user;
    }
}
