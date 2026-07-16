<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = collect([
            'dashboard.view',
            'users.view',
            'users.create',
            'users.update',
            'users.deactivate',
        ])->mapWithKeys(fn (string $permission) => [
            $permission => Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]),
        ]);

        $administrator = Role::firstOrCreate(['name' => 'Administrator', 'guard_name' => 'web']);
        $teacher = Role::firstOrCreate(['name' => 'Teacher', 'guard_name' => 'web']);
        $student = Role::firstOrCreate(['name' => 'Student', 'guard_name' => 'web']);
        $guardian = Role::firstOrCreate(['name' => 'Parent/Guardian', 'guard_name' => 'web']);

        $administrator->syncPermissions($permissions->values());
        $teacher->syncPermissions([$permissions['dashboard.view']]);
        $student->syncPermissions([$permissions['dashboard.view']]);
        $guardian->syncPermissions([$permissions['dashboard.view']]);

        $this->seedUser(
            name: 'Academify Administrator',
            email: 'admin@academify.local',
            jobTitle: 'System Administrator',
            role: $administrator,
        );

        $this->seedUser(
            name: 'Taylor Teacher',
            email: 'teacher@academify.local',
            jobTitle: 'Teacher',
            role: $teacher,
        );

        $this->seedUser(
            name: 'Sam Student',
            email: 'student@academify.local',
            jobTitle: 'Student',
            role: $student,
        );

        $this->seedUser(
            name: 'Pat Guardian',
            email: 'guardian@academify.local',
            jobTitle: 'Parent/Guardian',
            role: $guardian,
        );
    }

    private function seedUser(string $name, string $email, string $jobTitle, Role $role): void
    {
        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make('password'),
                'status' => User::STATUS_ACTIVE,
                'job_title' => $jobTitle,
                'email_verified_at' => now(),
            ],
        );

        $user->syncRoles([$role]);
    }
}
