<x-layouts::app :title="$gradingPeriod->name">
    <div class="mx-auto flex w-full max-w-7xl flex-1 flex-col gap-6 p-6">
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center"><div><flux:heading size="xl">{{ $gradingPeriod->name }}</flux:heading><flux:subheading>{{ $gradingPeriod->schoolYear->name }}</flux:subheading></div>@can('grades.update')<flux:button :href="route('grades.grading-periods.edit', $gradingPeriod)" wire:navigate variant="primary">{{ __('Edit Period') }}</flux:button>@endcan</div>
        @if(session('status'))<div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>@endif
        <section class="rounded-lg border border-zinc-200 bg-white p-5 shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
            <h2 class="font-semibold">{{ __('Details') }}</h2>
            <dl class="mt-4 space-y-3 text-sm"><div><dt class="text-zinc-500">{{ __('Status') }}</dt><dd><x-records.partials.status-badge :status="$gradingPeriod->status" /></dd></div><div><dt class="text-zinc-500">{{ __('Dates') }}</dt><dd>{{ $gradingPeriod->starts_at?->toFormattedDateString() ?? __('Not set') }} - {{ $gradingPeriod->ends_at?->toFormattedDateString() ?? __('Not set') }}</dd></div><div><dt class="text-zinc-500">{{ __('Assessments') }}</dt><dd>{{ $gradingPeriod->assessments->count() }}</dd></div></dl>
        </section>
    </div>
</x-layouts::app>
