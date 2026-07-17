<?php

namespace App\Models;

use Database\Factories\EnrollmentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $student_id
 * @property int $school_year_id
 * @property int $grade_level_id
 * @property int $section_id
 * @property string $status
 * @property Carbon|null $enrolled_at
 * @property Carbon|null $withdrawn_at
 * @property Carbon|null $completed_at
 * @property string|null $notes
 */
#[Fillable([
    'student_id',
    'school_year_id',
    'grade_level_id',
    'section_id',
    'status',
    'enrolled_at',
    'withdrawn_at',
    'completed_at',
    'notes',
])]
class Enrollment extends Model
{
    /** @use HasFactory<EnrollmentFactory> */
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_ENROLLED = 'enrolled';

    public const STATUS_WITHDRAWN = 'withdrawn';

    public const STATUS_COMPLETED = 'completed';

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * @return BelongsTo<SchoolYear, $this>
     */
    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
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
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'enrolled_at' => 'date',
            'withdrawn_at' => 'date',
            'completed_at' => 'date',
        ];
    }
}
