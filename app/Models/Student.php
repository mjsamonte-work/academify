<?php

namespace App\Models;

use App\Models\Concerns\HasActiveStatus;
use Database\Factories\StudentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $student_number
 * @property string $first_name
 * @property string|null $middle_name
 * @property string $last_name
 * @property string|null $preferred_name
 * @property Carbon|null $birthdate
 * @property string|null $gender
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $address
 * @property int|null $grade_level_id
 * @property int|null $section_id
 * @property string $status
 */
#[Fillable([
    'student_number',
    'first_name',
    'middle_name',
    'last_name',
    'preferred_name',
    'birthdate',
    'gender',
    'email',
    'phone',
    'address',
    'grade_level_id',
    'section_id',
    'status',
])]
class Student extends Model
{
    /** @use HasFactory<StudentFactory> */
    use HasFactory;

    public const STATUS_ACTIVE = HasActiveStatus::STATUS_ACTIVE;

    public const STATUS_INACTIVE = HasActiveStatus::STATUS_INACTIVE;

    public function fullName(): string
    {
        return collect([$this->first_name, $this->middle_name, $this->last_name])
            ->filter()
            ->implode(' ');
    }

    /**
     * @return BelongsTo<GradeLevel, $this>
     */
    public function gradeLevel(): BelongsTo
    {
        return $this->belongsTo(GradeLevel::class);
    }

    /**
     * @return BelongsTo<Section, $this>
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * @return BelongsToMany<Guardian, $this>
     */
    public function guardians(): BelongsToMany
    {
        return $this->belongsToMany(Guardian::class)
            ->withPivot(['relationship', 'is_primary_contact', 'can_pick_up', 'receives_notifications'])
            ->withTimestamps();
    }

    /**
     * @return HasMany<Enrollment, $this>
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * @return HasOne<Enrollment, $this>
     */
    public function currentEnrollment(): HasOne
    {
        return $this->hasOne(Enrollment::class)
            ->where('status', Enrollment::STATUS_ENROLLED)
            ->latestOfMany();
    }

    /**
     * @return HasMany<AttendanceRecord, $this>
     */
    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    /**
     * @return HasMany<StudentGrade, $this>
     */
    public function studentGrades(): HasMany
    {
        return $this->hasMany(StudentGrade::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'birthdate' => 'date',
        ];
    }
}
