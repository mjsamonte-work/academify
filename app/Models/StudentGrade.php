<?php

namespace App\Models;

use Database\Factories\StudentGradeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $assessment_id
 * @property int $student_id
 * @property string|null $score
 * @property string|null $remarks
 * @property string $status
 * @property int|null $submitted_by
 * @property Carbon|null $submitted_at
 */
#[Fillable(['assessment_id', 'student_id', 'score', 'remarks', 'status', 'submitted_by', 'submitted_at'])]
class StudentGrade extends Model
{
    /** @use HasFactory<StudentGradeFactory> */
    use HasFactory;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_SUBMITTED = 'submitted';

    public const STATUS_PUBLISHED = 'published';

    /**
     * @return BelongsTo<Assessment, $this>
     */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'submitted_at' => 'datetime',
        ];
    }
}
