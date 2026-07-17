<x-layouts::app :title="__('Student Attendance')">
    <div class="mx-auto flex w-full max-w-7xl flex-1 flex-col gap-6 p-6">
        <div>
            <flux:heading size="xl">{{ __('Student Attendance') }}</flux:heading>
            <flux:subheading>{{ $student->fullName() }} · {{ $student->student_number }}</flux:subheading>
        </div>

        <section class="rounded-lg border border-zinc-200 bg-white shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-zinc-200 text-xs uppercase text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
                        <tr>
                            <th class="px-4 py-3">{{ __('Date') }}</th>
                            <th class="px-4 py-3">{{ __('Class') }}</th>
                            <th class="px-4 py-3">{{ __('Section') }}</th>
                            <th class="px-4 py-3">{{ __('Status') }}</th>
                            <th class="px-4 py-3">{{ __('Notes') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse ($records as $record)
                            <tr>
                                <td class="px-4 py-4">{{ $record->session->attendance_date->toFormattedDateString() }}</td>
                                <td class="px-4 py-4">{{ $record->session->classSchedule->subject->name }}</td>
                                <td class="px-4 py-4">{{ $record->session->classSchedule->section->code }}</td>
                                <td class="px-4 py-4"><x-records.partials.status-badge :status="$record->status" /></td>
                                <td class="px-4 py-4">{{ $record->notes ?? __('No notes') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-10 text-center text-zinc-500">{{ __('No attendance history found.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-layouts::app>
