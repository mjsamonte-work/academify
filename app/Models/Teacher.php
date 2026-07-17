<?php

namespace App\Models;

use App\Models\Concerns\HasActiveStatus;
use Database\Factories\TeacherFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $employee_number
 * @property int|null $user_id
 * @property string $first_name
 * @property string|null $middle_name
 * @property string $last_name
 * @property string|null $preferred_name
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $address
 * @property string|null $job_title
 * @property string|null $department
 * @property string|null $employment_type
 * @property Carbon|null $hired_at
 * @property string $status
 */
#[Fillable([
    'employee_number',
    'user_id',
    'first_name',
    'middle_name',
    'last_name',
    'preferred_name',
    'email',
    'phone',
    'address',
    'job_title',
    'department',
    'employment_type',
    'hired_at',
    'status',
])]
class Teacher extends Model
{
    /** @use HasFactory<TeacherFactory> */
    use HasFactory;

    public const STATUS_ACTIVE = HasActiveStatus::STATUS_ACTIVE;

    public const STATUS_INACTIVE = HasActiveStatus::STATUS_INACTIVE;

    public const EMPLOYMENT_FULL_TIME = 'full_time';

    public const EMPLOYMENT_PART_TIME = 'part_time';

    public const EMPLOYMENT_CONTRACT = 'contract';

    public function fullName(): string
    {
        return collect([$this->first_name, $this->middle_name, $this->last_name])
            ->filter()
            ->implode(' ');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsToMany<Subject, $this>
     */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'teacher_subject')->withTimestamps();
    }

    /**
     * @return HasMany<ClassSchedule, $this>
     */
    public function classSchedules(): HasMany
    {
        return $this->hasMany(ClassSchedule::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'hired_at' => 'date',
        ];
    }
}
