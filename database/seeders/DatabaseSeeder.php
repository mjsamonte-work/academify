<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\GradeLevel;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Term;
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
            'academic_setup.view',
            'academic_setup.create',
            'academic_setup.update',
            'academic_setup.deactivate',
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

        $administrator->givePermissionTo([
            $permissions['academic_setup.view'],
            $permissions['academic_setup.create'],
            $permissions['academic_setup.update'],
            $permissions['academic_setup.deactivate'],
        ]);

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

        $this->seedAcademicSetup();
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

    private function seedAcademicSetup(): void
    {
        $schoolYear = SchoolYear::firstOrCreate(
            ['name' => '2026-2027'],
            [
                'starts_at' => '2026-06-01',
                'ends_at' => '2027-03-31',
                'is_active' => true,
                'status' => SchoolYear::STATUS_ACTIVE,
            ],
        );

        $schoolYear->update(['is_active' => true, 'status' => SchoolYear::STATUS_ACTIVE]);

        Term::firstOrCreate(
            ['school_year_id' => $schoolYear->id, 'name' => 'First Term'],
            [
                'starts_at' => '2026-06-01',
                'ends_at' => '2026-10-31',
                'sort_order' => 1,
                'status' => Term::STATUS_ACTIVE,
            ],
        );

        Term::firstOrCreate(
            ['school_year_id' => $schoolYear->id, 'name' => 'Second Term'],
            [
                'starts_at' => '2026-11-01',
                'ends_at' => '2027-03-31',
                'sort_order' => 2,
                'status' => Term::STATUS_ACTIVE,
            ],
        );

        $gradeOne = GradeLevel::firstOrCreate(
            ['code' => 'G1'],
            ['name' => 'Grade 1', 'sort_order' => 1, 'status' => GradeLevel::STATUS_ACTIVE],
        );

        GradeLevel::firstOrCreate(
            ['code' => 'G2'],
            ['name' => 'Grade 2', 'sort_order' => 2, 'status' => GradeLevel::STATUS_ACTIVE],
        );

        Section::firstOrCreate(
            ['code' => 'G1-A'],
            [
                'grade_level_id' => $gradeOne->id,
                'name' => 'Section A',
                'capacity' => 35,
                'status' => Section::STATUS_ACTIVE,
            ],
        );

        Subject::firstOrCreate(
            ['code' => 'MATH'],
            [
                'name' => 'Mathematics',
                'description' => 'Foundational mathematics subject.',
                'status' => Subject::STATUS_ACTIVE,
            ],
        );

        Subject::firstOrCreate(
            ['code' => 'SCI'],
            [
                'name' => 'Science',
                'description' => 'Foundational science subject.',
                'status' => Subject::STATUS_ACTIVE,
            ],
        );

        Classroom::firstOrCreate(
            ['code' => 'RM-101'],
            [
                'name' => 'Room 101',
                'capacity' => 35,
                'location' => 'Main Building',
                'status' => Classroom::STATUS_ACTIVE,
            ],
        );
    }
}
