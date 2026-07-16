@props(['status'])

<span @class([
    'rounded-full px-2 py-1 text-xs font-medium',
    'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' => $status === \App\Models\Student::STATUS_ACTIVE,
    'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300' => $status !== \App\Models\Student::STATUS_ACTIVE,
])>
    {{ str($status)->headline() }}
</span>
