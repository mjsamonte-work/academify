<x-layouts::app :title="__('Grading Periods')">
    <div class="mx-auto flex w-full max-w-7xl flex-1 flex-col gap-6 p-6">
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
            <div><flux:heading size="xl">{{ __('Grading Periods') }}</flux:heading><flux:subheading>{{ __('Manage school year grading windows.') }}</flux:subheading></div>
            @can('grades.create')<flux:button :href="route('grades.grading-periods.create')" wire:navigate variant="primary">{{ __('Create Period') }}</flux:button>@endcan
        </div>
        <section class="rounded-lg border border-zinc-200 bg-white shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
            <form method="GET" action="{{ route('grades.grading-periods.index') }}" class="grid gap-3 border-b border-zinc-200 p-4 dark:border-zinc-700 md:grid-cols-[1fr_180px_160px_auto]">
                <div></div>
                <select name="school_year_id" class="h-10 rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950"><option value="">{{ __('All years') }}</option>@foreach($schoolYears as $schoolYear)<option value="{{ $schoolYear->id }}" @selected((int) request('school_year_id') === $schoolYear->id)>{{ $schoolYear->name }}</option>@endforeach</select>
                <select name="status" class="h-10 rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950"><option value="">{{ __('All statuses') }}</option>@foreach($statuses as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ str($status)->headline() }}</option>@endforeach</select>
                <flux:button type="submit">{{ __('Filter') }}</flux:button>
            </form>
            <div class="overflow-x-auto"><table class="w-full text-left text-sm">
                <thead class="border-b border-zinc-200 text-xs uppercase text-zinc-500 dark:border-zinc-700 dark:text-zinc-400"><tr><th class="px-4 py-3">{{ __('Period') }}</th><th class="px-4 py-3">{{ __('Dates') }}</th><th class="px-4 py-3">{{ __('Status') }}</th><th class="px-4 py-3 text-right">{{ __('Actions') }}</th></tr></thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">@forelse($gradingPeriods as $period)<tr><td class="px-4 py-4"><p class="font-medium">{{ $period->name }}</p><p class="text-zinc-500">{{ $period->schoolYear->name }}</p></td><td class="px-4 py-4">{{ $period->starts_at?->toFormattedDateString() ?? __('Not set') }} - {{ $period->ends_at?->toFormattedDateString() ?? __('Not set') }}</td><td class="px-4 py-4"><x-records.partials.status-badge :status="$period->status" /></td><td class="px-4 py-4 text-right"><div class="flex justify-end gap-2"><flux:button size="sm" :href="route('grades.grading-periods.show', $period)" wire:navigate>{{ __('View') }}</flux:button>@can('grades.update')<flux:button size="sm" :href="route('grades.grading-periods.edit', $period)" wire:navigate>{{ __('Edit') }}</flux:button>@endcan</div></td></tr>@empty<tr><td colspan="4" class="px-4 py-10 text-center text-zinc-500">{{ __('No grading periods found.') }}</td></tr>@endforelse</tbody>
            </table></div><div class="border-t border-zinc-200 p-4 dark:border-zinc-700">{{ $gradingPeriods->links() }}</div>
        </section>
    </div>
</x-layouts::app>
