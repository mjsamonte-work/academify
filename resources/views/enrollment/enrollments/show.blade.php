<x-layouts::app :title="$enrollment->student->fullName()">
    <div class="mx-auto flex w-full max-w-7xl flex-1 flex-col gap-6 p-6">
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
            <div>
                <flux:heading size="xl">{{ $enrollment->student->fullName() }}</flux:heading>
                <flux:subheading>{{ $enrollment->student->student_number }} · {{ $enrollment->schoolYear->name }}</flux:subheading>
            </div>
            @can('enrollments.update')
                <flux:button :href="route('enrollment.enrollments.edit', $enrollment)" wire:navigate variant="primary">{{ __('Edit Enrollment') }}</flux:button>
            @endcan
        </div>

        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200">{{ session('status') }}</div>
        @endif

        <div class="grid gap-6 lg:grid-cols-[0.8fr_1.2fr]">
            <section class="rounded-lg border border-zinc-200 bg-white p-5 shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
                <h2 class="font-semibold">{{ __('Enrollment') }}</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div><dt class="text-zinc-500">{{ __('Status') }}</dt><dd><x-records.partials.status-badge :status="$enrollment->status" /></dd></div>
                    <div><dt class="text-zinc-500">{{ __('School Year') }}</dt><dd>{{ $enrollment->schoolYear->name }}</dd></div>
                    <div><dt class="text-zinc-500">{{ __('Placement') }}</dt><dd>{{ $enrollment->gradeLevel->name }} · {{ $enrollment->section->code }}</dd></div>
                    <div><dt class="text-zinc-500">{{ __('Enrolled') }}</dt><dd>{{ $enrollment->enrolled_at?->toFormattedDateString() ?? __('Not provided') }}</dd></div>
                    <div><dt class="text-zinc-500">{{ __('Withdrawn') }}</dt><dd>{{ $enrollment->withdrawn_at?->toFormattedDateString() ?? __('Not applicable') }}</dd></div>
                    <div><dt class="text-zinc-500">{{ __('Completed') }}</dt><dd>{{ $enrollment->completed_at?->toFormattedDateString() ?? __('Not applicable') }}</dd></div>
                    <div><dt class="text-zinc-500">{{ __('Notes') }}</dt><dd>{{ $enrollment->notes ?? __('No notes') }}</dd></div>
                </dl>
            </section>

            <section class="rounded-lg border border-zinc-200 bg-white shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
                <div class="border-b border-zinc-200 px-5 py-4 dark:border-zinc-700"><h2 class="font-semibold">{{ __('Student Enrollment History') }}</h2></div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-zinc-200 text-xs uppercase text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
                            <tr>
                                <th class="px-5 py-3">{{ __('School Year') }}</th>
                                <th class="px-5 py-3">{{ __('Placement') }}</th>
                                <th class="px-5 py-3">{{ __('Status') }}</th>
                                <th class="px-5 py-3">{{ __('Date') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                            @foreach ($history as $record)
                                <tr>
                                    <td class="px-5 py-4">{{ $record->schoolYear->name }}</td>
                                    <td class="px-5 py-4">{{ $record->gradeLevel->name }} / {{ $record->section->code }}</td>
                                    <td class="px-5 py-4"><x-records.partials.status-badge :status="$record->status" /></td>
                                    <td class="px-5 py-4">{{ $record->enrolled_at?->toFormattedDateString() ?? __('No date') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</x-layouts::app>
