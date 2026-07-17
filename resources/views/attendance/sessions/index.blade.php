<x-layouts::app :title="__('Attendance Sessions')">
    <div class="mx-auto flex w-full max-w-7xl flex-1 flex-col gap-6 p-6">
        <div>
            <flux:heading size="xl">{{ __('Attendance Sessions') }}</flux:heading>
            <flux:subheading>{{ __('Review class attendance activity by date, section, teacher, and status.') }}</flux:subheading>
        </div>

        <section class="rounded-lg border border-zinc-200 bg-white shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
            <form method="GET" action="{{ route('attendance.sessions.index') }}" class="grid gap-3 border-b border-zinc-200 p-4 dark:border-zinc-700 md:grid-cols-4 xl:grid-cols-[140px_140px_150px_150px_170px_170px_130px_auto]">
                <input name="date_from" type="date" value="{{ request('date_from') }}" class="h-10 rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                <input name="date_to" type="date" value="{{ request('date_to') }}" class="h-10 rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                <select name="school_year_id" class="h-10 rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                    <option value="">{{ __('All years') }}</option>
                    @foreach ($schoolYears as $schoolYear)
                        <option value="{{ $schoolYear->id }}" @selected((int) request('school_year_id') === $schoolYear->id)>{{ $schoolYear->name }}</option>
                    @endforeach
                </select>
                <select name="section_id" class="h-10 rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                    <option value="">{{ __('All sections') }}</option>
                    @foreach ($sections as $section)
                        <option value="{{ $section->id }}" @selected((int) request('section_id') === $section->id)>{{ $section->code }}</option>
                    @endforeach
                </select>
                <select name="teacher_id" class="h-10 rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                    <option value="">{{ __('All teachers') }}</option>
                    @foreach ($teachers as $teacher)
                        <option value="{{ $teacher->id }}" @selected((int) request('teacher_id') === $teacher->id)>{{ $teacher->fullName() }}</option>
                    @endforeach
                </select>
                <select name="class_schedule_id" class="h-10 rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                    <option value="">{{ __('All classes') }}</option>
                    @foreach ($classSchedules as $classSchedule)
                        <option value="{{ $classSchedule->id }}" @selected((int) request('class_schedule_id') === $classSchedule->id)>{{ $classSchedule->subject->code }} · {{ $classSchedule->section->code }}</option>
                    @endforeach
                </select>
                <select name="status" class="h-10 rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                    <option value="">{{ __('All statuses') }}</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ str($status)->headline() }}</option>
                    @endforeach
                </select>
                <flux:button type="submit">{{ __('Filter') }}</flux:button>
            </form>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-zinc-200 text-xs uppercase text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
                        <tr>
                            <th class="px-4 py-3">{{ __('Date') }}</th>
                            <th class="px-4 py-3">{{ __('Class') }}</th>
                            <th class="px-4 py-3">{{ __('Teacher') }}</th>
                            <th class="px-4 py-3">{{ __('Records') }}</th>
                            <th class="px-4 py-3">{{ __('Status') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse ($sessions as $session)
                            <tr>
                                <td class="px-4 py-4">{{ $session->attendance_date->toFormattedDateString() }}</td>
                                <td class="px-4 py-4">{{ $session->classSchedule->subject->code }} · {{ $session->classSchedule->section->code }}</td>
                                <td class="px-4 py-4">{{ $session->classSchedule->teacher->fullName() }}</td>
                                <td class="px-4 py-4">{{ $session->records()->count() }}</td>
                                <td class="px-4 py-4"><x-records.partials.status-badge :status="$session->status" /></td>
                                <td class="px-4 py-4 text-right">
                                    <flux:button size="sm" :href="route('attendance.sessions.show', $session)" wire:navigate>{{ __('View') }}</flux:button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-10 text-center text-zinc-500">{{ __('No attendance sessions found.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-zinc-200 p-4 dark:border-zinc-700">{{ $sessions->links() }}</div>
        </section>
    </div>
</x-layouts::app>
