<x-layouts::app :title="__('Attendance Session')">
    <div class="mx-auto flex w-full max-w-7xl flex-1 flex-col gap-6 p-6">
        <div>
            <flux:heading size="xl">{{ __('Attendance Session') }}</flux:heading>
            <flux:subheading>{{ $session->classSchedule->subject->name }} · {{ $session->attendance_date->toFormattedDateString() }}</flux:subheading>
        </div>

        <div class="grid gap-6 lg:grid-cols-[0.8fr_1.2fr]">
            <section class="rounded-lg border border-zinc-200 bg-white p-5 shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
                <h2 class="font-semibold">{{ __('Session Details') }}</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div><dt class="text-zinc-500">{{ __('Status') }}</dt><dd><x-records.partials.status-badge :status="$session->status" /></dd></div>
                    <div><dt class="text-zinc-500">{{ __('Class') }}</dt><dd>{{ $session->classSchedule->subject->code }} · {{ $session->classSchedule->section->code }}</dd></div>
                    <div><dt class="text-zinc-500">{{ __('Teacher') }}</dt><dd>{{ $session->classSchedule->teacher->fullName() }}</dd></div>
                    <div><dt class="text-zinc-500">{{ __('Schedule') }}</dt><dd>{{ str($session->classSchedule->day_of_week)->headline() }} · {{ substr($session->classSchedule->starts_at, 0, 5) }} - {{ substr($session->classSchedule->ends_at, 0, 5) }}</dd></div>
                    <div><dt class="text-zinc-500">{{ __('Submitted By') }}</dt><dd>{{ $session->submittedBy?->name ?? __('Not submitted') }}</dd></div>
                    <div><dt class="text-zinc-500">{{ __('Notes') }}</dt><dd>{{ $session->notes ?? __('No notes') }}</dd></div>
                </dl>
            </section>

            <section class="rounded-lg border border-zinc-200 bg-white shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
                <div class="border-b border-zinc-200 px-5 py-4 dark:border-zinc-700"><h2 class="font-semibold">{{ __('Attendance Records') }}</h2></div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-zinc-200 text-xs uppercase text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
                            <tr>
                                <th class="px-5 py-3">{{ __('Student') }}</th>
                                <th class="px-5 py-3">{{ __('Status') }}</th>
                                <th class="px-5 py-3">{{ __('Notes') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                            @forelse ($session->records as $record)
                                <tr>
                                    <td class="px-5 py-4">
                                        <a href="{{ route('attendance.students.show', $record->student) }}" class="font-medium text-zinc-950 hover:underline dark:text-white" wire:navigate>{{ $record->student->fullName() }}</a>
                                        <p class="text-zinc-500">{{ $record->student->student_number }}</p>
                                    </td>
                                    <td class="px-5 py-4"><x-records.partials.status-badge :status="$record->status" /></td>
                                    <td class="px-5 py-4">{{ $record->notes ?? __('No notes') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-5 py-10 text-center text-zinc-500">{{ __('No attendance records found.') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</x-layouts::app>
