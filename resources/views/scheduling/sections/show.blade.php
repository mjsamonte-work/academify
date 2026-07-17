<x-layouts::app :title="__('Section Schedule')">
    <div class="mx-auto flex w-full max-w-7xl flex-1 flex-col gap-6 p-6">
        <div>
            <flux:heading size="xl">{{ __('Section Schedule') }}</flux:heading>
            <flux:subheading>{{ $section->gradeLevel?->name }} · {{ $section->code }}</flux:subheading>
        </div>

        <section class="rounded-lg border border-zinc-200 bg-white shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-zinc-200 text-xs uppercase text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
                        <tr>
                            <th class="px-4 py-3">{{ __('Day') }}</th>
                            <th class="px-4 py-3">{{ __('Time') }}</th>
                            <th class="px-4 py-3">{{ __('Subject') }}</th>
                            <th class="px-4 py-3">{{ __('Teacher') }}</th>
                            <th class="px-4 py-3">{{ __('Room') }}</th>
                            <th class="px-4 py-3">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse ($classSchedules as $classSchedule)
                            <tr>
                                <td class="px-4 py-4">{{ str($classSchedule->day_of_week)->headline() }}</td>
                                <td class="px-4 py-4">{{ substr($classSchedule->starts_at, 0, 5) }} - {{ substr($classSchedule->ends_at, 0, 5) }}</td>
                                <td class="px-4 py-4">{{ $classSchedule->subject->name }}</td>
                                <td class="px-4 py-4">{{ $classSchedule->teacher->fullName() }}</td>
                                <td class="px-4 py-4">{{ $classSchedule->classroom?->code ?? __('No room') }}</td>
                                <td class="px-4 py-4"><x-records.partials.status-badge :status="$classSchedule->status" /></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-10 text-center text-zinc-500">{{ __('No schedules found for this section.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-layouts::app>
