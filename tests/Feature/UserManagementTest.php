<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_creates_roles_permissions_and_administrator(): void
    {
        $this->seed();

        $this->assertDatabaseHas('roles', ['name' => 'Administrator']);
        $this->assertDatabaseHas('roles', ['name' => 'Teacher']);
        $this->assertDatabaseHas('roles', ['name' => 'Student']);
        $this->assertDatabaseHas('roles', ['name' => 'Parent/Guardian']);
        $this->assertDatabaseHas('permissions', ['name' => 'users.view']);

        $admin = User::where('email', 'admin@academify.local')->firstOrFail();
        $teacher = User::where('email', 'teacher@academify.local')->firstOrFail();
        $student = User::where('email', 'student@academify.local')->firstOrFail();
        $guardian = User::where('email', 'guardian@academify.local')->firstOrFail();

        $this->assertTrue($admin->hasRole('Administrator'));
        $this->assertTrue($admin->can('users.create'));
        $this->assertTrue($teacher->hasRole('Teacher'));
        $this->assertTrue($student->hasRole('Student'));
        $this->assertTrue($guardian->hasRole('Parent/Guardian'));
    }

    public function test_administrators_can_create_users_and_assign_roles(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Jane Teacher',
            'email' => 'jane.teacher@academify.local',
            'password' => 'password',
            'password_confirmation' => 'password',
            'status' => User::STATUS_ACTIVE,
            'phone' => '555-0100',
            'job_title' => 'Teacher',
            'roles' => ['Teacher'],
        ]);

        $user = User::where('email', 'jane.teacher@academify.local')->firstOrFail();

        $response->assertRedirect(route('admin.users.show', $user));
        $this->assertTrue($user->hasRole('Teacher'));
        $this->assertSame(User::STATUS_ACTIVE, $user->status);
    }

    public function test_administrators_can_update_users_and_deactivate_accounts(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();
        $user = User::factory()->create();
        $user->assignRole(Role::findByName('Student'));

        $response = $this->actingAs($admin)->put(route('admin.users.update', $user), [
            'name' => 'Updated Student',
            'email' => $user->email,
            'password' => '',
            'password_confirmation' => '',
            'status' => User::STATUS_INACTIVE,
            'phone' => '',
            'job_title' => 'Student',
            'roles' => ['Student'],
        ]);

        $response->assertRedirect(route('admin.users.show', $user));

        $user->refresh();
        $this->assertSame('Updated Student', $user->name);
        $this->assertSame(User::STATUS_INACTIVE, $user->status);
        $this->assertTrue($user->hasRole('Student'));
    }

    public function test_non_administrators_can_not_access_user_management(): void
    {
        $this->seed();
        $user = User::factory()->create();
        $user->assignRole(Role::findByName('Teacher'));

        $this->actingAs($user)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }
}
