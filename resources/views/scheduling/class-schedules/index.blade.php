<x-layouts::app :title="__('Class Schedules')">
    <div class="mx-auto flex w-full max-w-7xl flex-1 flex-col gap-6 p-6">
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
            <div>
                <flux:heading size="xl">{{ __('Class Schedules') }}</flux:heading>
                <flux:subheading>{{ __('Manage section, teacher, subject, room, and meeting time assignments.') }}</flux:subheading>
            </div>
            @can('schedules.create')
                <flux:button :href="route('scheduling.class-schedules.create')" wire:navigate variant="primary">{{ __('Create Schedule') }}</flux:button>
            @endcan
        </div>

        <section class="rounded-lg border border-zinc-200 bg-white shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
            <form method="GET" action="{{ route('scheduling.class-schedules.index') }}" class="grid gap-3 border-b border-zinc-200 p-4 dark:border-zinc-700 md:grid-cols-4 xl:grid-cols-[150px_160px_160px_160px_150px_140px_140px_auto]">
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
                <select name="subject_id" class="h-10 rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                    <option value="">{{ __('All subjects') }}</option>
                    @foreach ($subjects as $subject)
                        <option value="{{ $subject->id }}" @selected((int) request('subject_id') === $subject->id)>{{ $subject->code }}</option>
                    @endforeach
                </select>
                <select name="classroom_id" class="h-10 rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                    <option value="">{{ __('All rooms') }}</option>
                    @foreach ($classrooms as $classroom)
                        <option value="{{ $classroom->id }}" @selected((int) request('classroom_id') === $classroom->id)>{{ $classroom->code }}</option>
                    @endforeach
                </select>
                <select name="day_of_week" class="h-10 rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                    <option value="">{{ __('All days') }}</option>
                    @foreach ($days as $day)
                        <option value="{{ $day }}" @selected(request('day_of_week') === $day)>{{ str($day)->headline() }}</option>
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
                            <th class="px-4 py-3">{{ __('Schedule') }}</th>
                            <th class="px-4 py-3">{{ __('Section') }}</th>
                            <th class="px-4 py-3">{{ __('Teacher') }}</th>
                            <th class="px-4 py-3">{{ __('Room') }}</th>
                            <th class="px-4 py-3">{{ __('Status') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse ($classSchedules as $classSchedule)
                            <tr>
                                <td class="px-4 py-4">
                                    <p class="font-medium text-zinc-950 dark:text-white">{{ $classSchedule->subject->code }} · {{ str($classSchedule->day_of_week)->headline() }}</p>
                                    <p class="text-zinc-500">{{ substr($classSchedule->starts_at, 0, 5) }} - {{ substr($classSchedule->ends_at, 0, 5) }} · {{ $classSchedule->schoolYear->name }}</p>
                                </td>
                                <td class="px-4 py-4">{{ $classSchedule->section->code }}</td>
                                <td class="px-4 py-4">{{ $classSchedule->teacher->fullName() }}</td>
                                <td class="px-4 py-4">{{ $classSchedule->classroom?->code ?? __('No room') }}</td>
                                <td class="px-4 py-4"><x-records.partials.status-badge :status="$classSchedule->status" /></td>
                                <td class="px-4 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <flux:button size="sm" :href="route('scheduling.class-schedules.show', $classSchedule)" wire:navigate>{{ __('View') }}</flux:button>
                                        @can('schedules.update')
                                            <flux:button size="sm" :href="route('scheduling.class-schedules.edit', $classSchedule)" wire:navigate>{{ __('Edit') }}</flux:button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-10 text-center text-zinc-500">{{ __('No schedules found.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-zinc-200 p-4 dark:border-zinc-700">{{ $classSchedules->links() }}</div>
        </section>
    </div>
</x-layouts::app>
