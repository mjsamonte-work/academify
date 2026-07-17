<x-layouts::app :title="__('My Schedule')">
    <div class="mx-auto flex w-full max-w-7xl flex-1 flex-col gap-6 p-6">
        <div>
            <flux:heading size="xl">{{ __('My Schedule') }}</flux:heading>
            <flux:subheading>{{ $teacher?->fullName() ?? __('Teacher schedule') }}</flux:subheading>
        </div>

        @if ($teacher)
            <section class="rounded-lg border border-zinc-200 bg-white shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-zinc-200 text-xs uppercase text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
                            <tr>
                                <th class="px-4 py-3">{{ __('Day') }}</th>
                                <th class="px-4 py-3">{{ __('Time') }}</th>
                                <th class="px-4 py-3">{{ __('Subject') }}</th>
                                <th class="px-4 py-3">{{ __('Section') }}</th>
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
                                    <td class="px-4 py-4">{{ $classSchedule->section->code }}</td>
                                    <td class="px-4 py-4">{{ $classSchedule->classroom?->code ?? __('No room') }}</td>
                                    <td class="px-4 py-4"><x-records.partials.status-badge :status="$classSchedule->status" /></td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-4 py-10 text-center text-zinc-500">{{ __('No schedules assigned yet.') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        @else
            <section class="rounded-lg border border-zinc-200 bg-white p-6 text-sm shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
                <p class="font-medium">{{ __('No teacher profile is linked to your account yet.') }}</p>
                <p class="mt-1 text-zinc-500">{{ __('Please contact an administrator to complete your teacher record.') }}</p>
            </section>
        @endif
    </div>
</x-layouts::app>
