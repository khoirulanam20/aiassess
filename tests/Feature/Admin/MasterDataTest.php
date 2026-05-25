<?php

namespace Tests\Feature\Admin;

use App\Models\Department;
use App\Models\Organization;
use App\Models\Position;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MasterDataTest extends TestCase
{
    use RefreshDatabase;

    private Organization $organization;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RbacSeeder::class);

        $this->organization = Organization::create([
            'name' => 'Test Org',
            'slug' => 'test-org',
            'is_active' => true,
        ]);

        $this->admin = User::factory()->create([
            'organization_id' => $this->organization->id,
        ]);
        $this->admin->assignRole('admin');
    }

    public function test_admin_can_create_department_and_position(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.master-data.departments.store'), [
            'name' => 'Finance',
            'code' => 'FIN',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.master-data.departments.index'));

        $department = Department::where('name', 'Finance')->first();
        $this->assertNotNull($department);

        $response = $this->actingAs($this->admin)->post(route('admin.master-data.positions.store'), [
            'name' => 'Accountant',
            'department_id' => $department->id,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.master-data.positions.index'));
        $this->assertDatabaseHas('positions', [
            'name' => 'Accountant',
            'department_id' => $department->id,
            'organization_id' => $this->organization->id,
        ]);
    }

    public function test_candidate_store_links_master_data(): void
    {
        $department = Department::create([
            'organization_id' => $this->organization->id,
            'name' => 'IT',
            'is_active' => true,
        ]);

        $position = Position::create([
            'organization_id' => $this->organization->id,
            'department_id' => $department->id,
            'name' => 'Developer',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.candidates.store'), [
            'name' => 'Budi Santoso',
            'email' => 'budi@test.com',
            'department_id' => $department->id,
            'position_id' => $position->id,
        ]);

        $response->assertRedirect(route('admin.candidates.index'));

        $user = User::where('email', 'budi@test.com')->first();
        $this->assertNotNull($user);
        $this->assertSame($department->id, $user->userDetail->department_id);
        $this->assertSame($position->id, $user->userDetail->position_id);
        $this->assertSame('IT', $user->userDetail->department);
        $this->assertSame('Developer', $user->userDetail->position);
    }

    public function test_position_must_belong_to_selected_department(): void
    {
        $engineering = Department::create([
            'organization_id' => $this->organization->id,
            'name' => 'Engineering',
            'is_active' => true,
        ]);

        $hr = Department::create([
            'organization_id' => $this->organization->id,
            'name' => 'HR',
            'is_active' => true,
        ]);

        $hrPosition = Position::create([
            'organization_id' => $this->organization->id,
            'department_id' => $hr->id,
            'name' => 'HR Specialist',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.candidates.store'), [
            'name' => 'Invalid Combo',
            'email' => 'invalid@test.com',
            'department_id' => $engineering->id,
            'position_id' => $hrPosition->id,
        ]);

        $response->assertSessionHasErrors('position_id');
    }

    public function test_superadmin_cannot_access_master_data(): void
    {
        $superadmin = User::factory()->create();
        $superadmin->assignRole('superadmin');

        $this->actingAs($superadmin)
            ->get(route('admin.master-data.index'))
            ->assertForbidden();
    }

    public function test_cannot_delete_department_in_use(): void
    {
        $department = Department::create([
            'organization_id' => $this->organization->id,
            'name' => 'Sales',
            'is_active' => true,
        ]);

        $candidate = User::factory()->create(['organization_id' => $this->organization->id]);
        $candidate->assignRole('candidate');
        UserDetail::create([
            'user_id' => $candidate->id,
            'department_id' => $department->id,
            'department' => $department->name,
            'language' => 'id',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.master-data.departments.destroy', $department));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('departments', ['id' => $department->id]);
    }
}
