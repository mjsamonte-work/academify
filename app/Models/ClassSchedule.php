<?php

namespace App\Models;

use App\Models\Concerns\HasActiveStatus;
use Database\Factories\ClassScheduleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $school_year_id
 * @property int $section_id
 * @property int $subject_id
 * @property int $teacher_id
 * @property int|null $classroom_id
 * @property string $day_of_week
 * @property string $starts_at
 * @property string $ends_at
 * @property string $status
 * @property string|null $notes
 */
#[Fillable([
    'school_year_id',
    'section_id',
    'subject_id',
    'teacher_id',
    'classroom_id',
    'day_of_week',
    'starts_at',
    'ends_at',
    'status',
    'notes',
])]
class ClassSchedule extends Model
{
    /** @use HasFactory<ClassScheduleFactory> */
    use HasFactory;

    public const STATUS_ACTIVE = HasActiveStatus::STATUS_ACTIVE;

    public const STATUS_INACTIVE = HasActiveStatus::STATUS_INACTIVE;

    public const DAY_MONDAY = 'monday';

    public const DAY_TUESDAY = 'tuesday';

    public const DAY_WEDNESDAY = 'wednesday';

    public const DAY_THURSDAY = 'thursday';

    public const DAY_FRIDAY = 'friday';

    public const DAY_SATURDAY = 'saturday';

    public const DAY_SUNDAY = 'sunday';

    /**
     * @return BelongsTo<SchoolYear, $this>
     */
    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }

    /**
     * @return BelongsTo<Section, $this>
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * @return BelongsTo<Subject, $this>
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * @return BelongsTo<Teacher, $this>
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * @return BelongsTo<Classroom, $this>
     */
    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    /**
     * @return HasMany<AttendanceSession, $this>
     */
    public function attendanceSessions(): HasMany
    {
        return $this->hasMany(AttendanceSession::class);
    }

    /**
     * @return array<int, string>
     */
    public static function days(): array
    {
        return [
            self::DAY_MONDAY,
            self::DAY_TUESDAY,
            self::DAY_WEDNESDAY,
            self::DAY_THURSDAY,
            self::DAY_FRIDAY,
            self::DAY_SATURDAY,
            self::DAY_SUNDAY,
        ];
    }
}
