<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $user_id
 * @property string $action
 * @property string $module
 * @property string|null $subject_type
 * @property int|null $subject_id
 * @property string|null $subject_label
 * @property array<string, mixed>|null $old_values
 * @property array<string, mixed>|null $new_values
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property Carbon|null $created_at
 */
#[Fillable([
    'user_id',
    'action',
    'module',
    'subject_type',
    'subject_id',
    'subject_label',
    'old_values',
    'new_values',
    'ip_address',
    'user_agent',
    'created_at',
])]
class ActivityLog extends Model
{
    public const ACTION_CREATED = 'created';

    public const ACTION_UPDATED = 'updated';

    public const ACTION_DEACTIVATED = 'deactivated';

    public const ACTION_ACTIVATED = 'activated';

    public const ACTION_SUBMITTED = 'submitted';

    public const ACTION_PUBLISHED = 'published';

    public const ACTION_ARCHIVED = 'archived';

    public const ACTION_WITHDRAWN = 'withdrawn';

    public const ACTION_COMPLETED = 'completed';

    public const MODULE_USERS = 'users';

    public const MODULE_ACADEMIC_SETUP = 'academic_setup';

    public const MODULE_STUDENTS = 'students';

    public const MODULE_GUARDIANS = 'guardians';

    public const MODULE_TEACHERS = 'teachers';

    public const MODULE_ENROLLMENTS = 'enrollments';

    public const MODULE_SCHEDULING = 'scheduling';

    public const MODULE_ATTENDANCE = 'attendance';

    public const MODULE_GRADES = 'grades';

    public const MODULE_ANNOUNCEMENTS = 'announcements';

    public const MODULE_SETTINGS = 'settings';

    public $timestamps = false;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return array<int, string>
     */
    public static function actions(): array
    {
        return [
            self::ACTION_CREATED,
            self::ACTION_UPDATED,
            self::ACTION_DEACTIVATED,
            self::ACTION_ACTIVATED,
            self::ACTION_SUBMITTED,
            self::ACTION_PUBLISHED,
            self::ACTION_ARCHIVED,
            self::ACTION_WITHDRAWN,
            self::ACTION_COMPLETED,
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function modules(): array
    {
        return [
            self::MODULE_USERS,
            self::MODULE_ACADEMIC_SETUP,
            self::MODULE_STUDENTS,
            self::MODULE_GUARDIANS,
            self::MODULE_TEACHERS,
            self::MODULE_ENROLLMENTS,
            self::MODULE_SCHEDULING,
            self::MODULE_ATTENDANCE,
            self::MODULE_GRADES,
            self::MODULE_ANNOUNCEMENTS,
            self::MODULE_SETTINGS,
        ];
    }
}
