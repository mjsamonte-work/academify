<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\Classroom;
use App\Models\ClassSchedule;
use App\Models\Enrollment;
use App\Models\GradeLevel;
use App\Models\GradingPeriod;
use App\Models\Guardian;
use App\Models\SchoolYear;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentGrade;
use App\Models\Subject;
use App\Models\Teacher as TeacherProfile;
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
            'students.view',
            'students.create',
            'students.update',
            'students.deactivate',
            'guardians.view',
            'guardians.create',
            'guardians.update',
            'guardians.deactivate',
            'teachers.view',
            'teachers.create',
            'teachers.update',
            'teachers.deactivate',
            'teachers.assign_subjects',
            'teachers.view_own',
            'enrollments.view',
            'enrollments.create',
            'enrollments.update',
            'enrollments.withdraw',
            'enrollments.complete',
            'schedules.view',
            'schedules.create',
            'schedules.update',
            'schedules.deactivate',
            'schedules.view_own',
            'attendance.view',
            'attendance.create',
            'attendance.update',
            'attendance.submit',
            'attendance.view_own',
            'grades.view',
            'grades.create',
            'grades.update',
            'grades.submit',
            'grades.publish',
            'grades.view_own',
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

        $administrator->syncPermissions($permissions->except(['teachers.view_own', 'schedules.view_own', 'attendance.view_own', 'grades.view_own'])->values());
        $teacher->syncPermissions([
            $permissions['dashboard.view'],
            $permissions['teachers.view_own'],
            $permissions['schedules.view_own'],
            $permissions['attendance.view_own'],
            $permissions['attendance.create'],
            $permissions['attendance.update'],
            $permissions['attendance.submit'],
            $permissions['grades.view_own'],
            $permissions['grades.create'],
            $permissions['grades.update'],
            $permissions['grades.submit'],
        ]);
        $student->syncPermissions([$permissions['dashboard.view']]);
        $guardian->syncPermissions([$permissions['dashboard.view']]);

        $administrator->givePermissionTo([
            $permissions['academic_setup.view'],
            $permissions['academic_setup.create'],
            $permissions['academic_setup.update'],
            $permissions['academic_setup.deactivate'],
            $permissions['students.view'],
            $permissions['students.create'],
            $permissions['students.update'],
            $permissions['students.deactivate'],
            $permissions['guardians.view'],
            $permissions['guardians.create'],
            $permissions['guardians.update'],
            $permissions['guardians.deactivate'],
            $permissions['teachers.view'],
            $permissions['teachers.create'],
            $permissions['teachers.update'],
            $permissions['teachers.deactivate'],
            $permissions['teachers.assign_subjects'],
            $permissions['enrollments.view'],
            $permissions['enrollments.create'],
            $permissions['enrollments.update'],
            $permissions['enrollments.withdraw'],
            $permissions['enrollments.complete'],
            $permissions['schedules.view'],
            $permissions['schedules.create'],
            $permissions['schedules.update'],
            $permissions['schedules.deactivate'],
            $permissions['attendance.view'],
            $permissions['attendance.create'],
            $permissions['attendance.update'],
            $permissions['attendance.submit'],
            $permissions['grades.view'],
            $permissions['grades.create'],
            $permissions['grades.update'],
            $permissions['grades.submit'],
            $permissions['grades.publish'],
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
        $this->seedStudentGuardianRecords();
        $this->seedTeacherRecords();
        $this->seedEnrollmentRecords();
        $this->seedScheduleRecords();
        $this->seedAttendanceRecords();
        $this->seedGradeRecords();
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

    private function seedStudentGuardianRecords(): void
    {
        $gradeLevel = GradeLevel::where('code', 'G1')->first();
        $section = Section::where('code', 'G1-A')->first();

        $student = Student::firstOrCreate(
            ['student_number' => 'STU-0001'],
            [
                'first_name' => 'Alex',
                'last_name' => 'Santos',
                'preferred_name' => 'Alex',
                'birthdate' => '2018-08-15',
                'gender' => 'Male',
                'email' => 'alex.santos@student.academify.local',
                'phone' => null,
                'address' => 'Main Building Area',
                'grade_level_id' => $gradeLevel?->id,
                'section_id' => $section?->id,
                'status' => Student::STATUS_ACTIVE,
            ],
        );

        $guardian = Guardian::firstOrCreate(
            ['email' => 'maria.santos@guardian.academify.local'],
            [
                'first_name' => 'Maria',
                'last_name' => 'Santos',
                'phone' => '555-0101',
                'alternate_phone' => null,
                'address' => 'Main Building Area',
                'occupation' => 'Parent',
                'status' => Guardian::STATUS_ACTIVE,
            ],
        );

        $student->guardians()->syncWithoutDetaching([
            $guardian->id => [
                'relationship' => 'Mother',
                'is_primary_contact' => true,
                'can_pick_up' => true,
                'receives_notifications' => true,
            ],
        ]);
    }

    private function seedTeacherRecords(): void
    {
        $teacherUser = User::where('email', 'teacher@academify.local')->first();
        $mathematics = Subject::where('code', 'MATH')->first();
        $science = Subject::where('code', 'SCI')->first();

        $teacher = TeacherProfile::firstOrCreate(
            ['employee_number' => 'TCH-0001'],
            [
                'user_id' => $teacherUser?->id,
                'first_name' => 'Taylor',
                'last_name' => 'Teacher',
                'preferred_name' => 'Taylor',
                'email' => 'teacher@academify.local',
                'phone' => '555-0301',
                'address' => 'Academify Faculty Office',
                'job_title' => 'Teacher',
                'department' => 'Elementary',
                'employment_type' => TeacherProfile::EMPLOYMENT_FULL_TIME,
                'hired_at' => '2026-06-01',
                'status' => TeacherProfile::STATUS_ACTIVE,
            ],
        );

        $teacher->update([
            'user_id' => $teacherUser?->id,
            'status' => TeacherProfile::STATUS_ACTIVE,
        ]);

        $teacher->subjects()->syncWithoutDetaching(
            collect([$mathematics?->id, $science?->id])->filter()->all(),
        );
    }

    private function seedEnrollmentRecords(): void
    {
        $student = Student::where('student_number', 'STU-0001')->first();
        $schoolYear = SchoolYear::where('name', '2026-2027')->first();
        $gradeLevel = GradeLevel::where('code', 'G1')->first();
        $section = Section::where('code', 'G1-A')->first();

        if (! $student || ! $schoolYear || ! $gradeLevel || ! $section) {
            return;
        }

        Enrollment::firstOrCreate(
            [
                'student_id' => $student->id,
                'school_year_id' => $schoolYear->id,
                'status' => Enrollment::STATUS_ENROLLED,
            ],
            [
                'grade_level_id' => $gradeLevel->id,
                'section_id' => $section->id,
                'enrolled_at' => '2026-06-01',
                'notes' => 'Seeded active enrollment for local development.',
            ],
        );
    }

    private function seedScheduleRecords(): void
    {
        $schoolYear = SchoolYear::where('name', '2026-2027')->first();
        $section = Section::where('code', 'G1-A')->first();
        $subject = Subject::where('code', 'MATH')->first();
        $teacher = TeacherProfile::where('employee_number', 'TCH-0001')->first();
        $classroom = Classroom::where('code', 'RM-101')->first();

        if (! $schoolYear || ! $section || ! $subject || ! $teacher) {
            return;
        }

        ClassSchedule::firstOrCreate(
            [
                'school_year_id' => $schoolYear->id,
                'section_id' => $section->id,
                'subject_id' => $subject->id,
                'teacher_id' => $teacher->id,
                'day_of_week' => ClassSchedule::DAY_MONDAY,
                'starts_at' => '08:00',
                'ends_at' => '09:00',
            ],
            [
                'classroom_id' => $classroom?->id,
                'status' => ClassSchedule::STATUS_ACTIVE,
                'notes' => 'Seeded class schedule for local development.',
            ],
        );
    }

    private function seedAttendanceRecords(): void
    {
        $classSchedule = ClassSchedule::query()
            ->where('day_of_week', ClassSchedule::DAY_MONDAY)
            ->where('starts_at', '08:00')
            ->first();
        $teacherUser = User::where('email', 'teacher@academify.local')->first();
        $student = Student::where('student_number', 'STU-0001')->first();

        if (! $classSchedule || ! $teacherUser || ! $student) {
            return;
        }

        $session = AttendanceSession::query()
            ->where('class_schedule_id', $classSchedule->id)
            ->whereDate('attendance_date', '2026-06-08')
            ->first();

        if (! $session) {
            $session = AttendanceSession::create([
                'class_schedule_id' => $classSchedule->id,
                'attendance_date' => '2026-06-08',
                'status' => AttendanceSession::STATUS_SUBMITTED,
                'submitted_by' => $teacherUser->id,
                'submitted_at' => now(),
                'notes' => 'Seeded attendance session for local development.',
            ]);
        }

        $session->records()->updateOrCreate(
            ['student_id' => $student->id],
            [
                'status' => AttendanceRecord::STATUS_PRESENT,
                'notes' => null,
            ],
        );
    }

    private function seedGradeRecords(): void
    {
        $schoolYear = SchoolYear::where('name', '2026-2027')->first();
        $classSchedule = ClassSchedule::query()->first();
        $student = Student::where('student_number', 'STU-0001')->first();
        $teacherUser = User::where('email', 'teacher@academify.local')->first();

        if (! $schoolYear || ! $classSchedule || ! $student || ! $teacherUser) {
            return;
        }

        $period = GradingPeriod::firstOrCreate(
            ['school_year_id' => $schoolYear->id, 'name' => 'First Quarter'],
            [
                'starts_at' => '2026-06-01',
                'ends_at' => '2026-08-31',
                'sort_order' => 1,
                'status' => GradingPeriod::STATUS_ACTIVE,
            ],
        );

        $assessment = Assessment::firstOrCreate(
            [
                'class_schedule_id' => $classSchedule->id,
                'grading_period_id' => $period->id,
                'title' => 'Quiz 1',
            ],
            [
                'assessment_type' => 'quiz',
                'max_score' => 100,
                'weight' => 10,
                'due_date' => '2026-06-15',
                'status' => Assessment::STATUS_DRAFT,
                'notes' => 'Seeded assessment for local development.',
            ],
        );

        $assessment->studentGrades()->updateOrCreate(
            ['student_id' => $student->id],
            [
                'score' => 95,
                'remarks' => 'Seeded grade for local development.',
                'status' => StudentGrade::STATUS_SUBMITTED,
                'submitted_by' => $teacherUser->id,
                'submitted_at' => now(),
            ],
        );
    }
}
