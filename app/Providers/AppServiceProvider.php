<?php

namespace App\Providers;

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
use App\Observers\ActivityLogObserver;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->registerActivityLogObservers();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }

    protected function registerActivityLogObservers(): void
    {
        foreach ([
            User::class,
            SchoolYear::class,
            Term::class,
            GradeLevel::class,
            Section::class,
            Subject::class,
            Classroom::class,
            Student::class,
            Guardian::class,
            Teacher::class,
            Enrollment::class,
            ClassSchedule::class,
            AttendanceSession::class,
            AttendanceRecord::class,
            GradingPeriod::class,
            Assessment::class,
            StudentGrade::class,
            Announcement::class,
            Setting::class,
        ] as $model) {
            $model::observe(ActivityLogObserver::class);
        }
    }
}
