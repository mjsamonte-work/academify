<?php

namespace App\Models;

use Database\Factories\AssessmentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $class_schedule_id
 * @property int $grading_period_id
 * @property string $title
 * @property string|null $assessment_type
 * @property string $max_score
 * @property string $weight
 * @property Carbon|null $due_date
 * @property string $status
 * @property string|null $notes
 */
#[Fillable([
    'class_schedule_id',
    'grading_period_id',
    'title',
    'assessment_type',
    'max_score',
    'weight',
    'due_date',
    'status',
    'notes',
])]
class Assessment extends Model
{
    /** @use HasFactory<AssessmentFactory> */
    use HasFactory;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_SUBMITTED = 'submitted';

    public const STATUS_PUBLISHED = 'published';

    /**
     * @return BelongsTo<ClassSchedule, $this>
     */
    public function classSchedule(): BelongsTo
    {
        return $this->belongsTo(ClassSchedule::class);
    }

    /**
     * @return BelongsTo<GradingPeriod, $this>
     */
    public function gradingPeriod(): BelongsTo
    {
        return $this->belongsTo(GradingPeriod::class);
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
            'due_date' => 'date',
            'max_score' => 'decimal:2',
            'weight' => 'decimal:2',
        ];
    }
}
