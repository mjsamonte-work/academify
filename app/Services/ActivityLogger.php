<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Announcement;
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
use App\Models\Setting;
use App\Models\Student;
use App\Models\StudentGrade;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Term;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    /**
     * @param  array<string, mixed>|null  $oldValues
     * @param  array<string, mixed>|null  $newValues
     */
    public function record(Model $subject, string $action, ?array $oldValues = null, ?array $newValues = null): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        ActivityLog::query()->create([
            'user_id' => $user->id,
            'action' => $action,
            'module' => $this->moduleFor($subject),
            'subject_type' => $subject::class,
            'subject_id' => $subject->getKey(),
            'subject_label' => $this->labelFor($subject),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'created_at' => now(),
        ]);
    }

    public function moduleFor(Model $subject): string
    {
        return match ($subject::class) {
            User::class => ActivityLog::MODULE_USERS,
            SchoolYear::class,
            Term::class,
            GradeLevel::class,
            Section::class,
            Subject::class,
            Classroom::class => ActivityLog::MODULE_ACADEMIC_SETUP,
            Student::class => ActivityLog::MODULE_STUDENTS,
            Guardian::class => ActivityLog::MODULE_GUARDIANS,
            Teacher::class => ActivityLog::MODULE_TEACHERS,
            Enrollment::class => ActivityLog::MODULE_ENROLLMENTS,
            ClassSchedule::class => ActivityLog::MODULE_SCHEDULING,
            AttendanceSession::class,
            AttendanceRecord::class => ActivityLog::MODULE_ATTENDANCE,
            GradingPeriod::class,
            Assessment::class,
            StudentGrade::class => ActivityLog::MODULE_GRADES,
            Announcement::class => ActivityLog::MODULE_ANNOUNCEMENTS,
            Setting::class => ActivityLog::MODULE_SETTINGS,
            default => str($subject::class)->classBasename()->snake()->plural()->toString(),
        };
    }

    private function labelFor(Model $subject): string
    {
        foreach (['title', 'name', 'code', 'student_number', 'employee_number', 'email', 'key'] as $attribute) {
            if (filled($subject->{$attribute} ?? null)) {
                return (string) $subject->{$attribute};
            }
        }

        if (method_exists($subject, 'fullName')) {
            return (string) $subject->fullName();
        }

        return str($subject::class)->classBasename()->headline().' #'.$subject->getKey();
    }
}
