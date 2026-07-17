@props(['status'])

<span @class([
    'rounded-full px-2 py-1 text-xs font-medium',
    'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' => in_array($status, [\App\Models\Student::STATUS_ACTIVE, \App\Models\Enrollment::STATUS_ENROLLED, \App\Models\AttendanceRecord::STATUS_PRESENT, \App\Models\AttendanceSession::STATUS_SUBMITTED], true),
    'bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300' => in_array($status, [\App\Models\Enrollment::STATUS_PENDING, \App\Models\AttendanceRecord::STATUS_LATE, \App\Models\AttendanceSession::STATUS_DRAFT], true),
    'bg-sky-100 text-sky-700 dark:bg-sky-500/15 dark:text-sky-300' => in_array($status, [\App\Models\Enrollment::STATUS_COMPLETED, \App\Models\AttendanceRecord::STATUS_EXCUSED], true),
    'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-300' => $status === \App\Models\AttendanceRecord::STATUS_ABSENT,
    'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300' => ! in_array($status, [\App\Models\Student::STATUS_ACTIVE, \App\Models\Enrollment::STATUS_ENROLLED, \App\Models\Enrollment::STATUS_PENDING, \App\Models\Enrollment::STATUS_COMPLETED, \App\Models\AttendanceRecord::STATUS_PRESENT, \App\Models\AttendanceRecord::STATUS_LATE, \App\Models\AttendanceRecord::STATUS_EXCUSED, \App\Models\AttendanceRecord::STATUS_ABSENT, \App\Models\AttendanceSession::STATUS_DRAFT, \App\Models\AttendanceSession::STATUS_SUBMITTED], true),
])>
    {{ str($status)->headline() }}
</span>
