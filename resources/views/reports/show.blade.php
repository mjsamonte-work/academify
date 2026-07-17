<x-layouts::app :title="$data['title']">
    <div class="mx-auto flex w-full max-w-7xl flex-1 flex-col gap-6 p-6">
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
            <div>
                <flux:heading size="xl">{{ $data['title'] }}</flux:heading>
                <flux:subheading>{{ __('Use filters to prepare a formal report preview before export.') }}</flux:subheading>
            </div>

            <div class="flex flex-wrap gap-2">
                @can('reports.export_pdf')
                    <flux:button :href="route('reports.export', array_merge(request()->except(['report', 'format']), ['report' => $reportKey, 'format' => 'pdf']))">
                        {{ __('Export PDF') }}
                    </flux:button>
                @endcan
                @can('reports.export_excel')
                    <flux:button :href="route('reports.export', array_merge(request()->except(['report', 'format']), ['report' => $reportKey, 'format' => 'excel']))" variant="primary">
                        {{ __('Export Excel') }}
                    </flux:button>
                @endcan
            </div>
        </div>

        <section class="rounded-lg border border-zinc-200 bg-white shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
            <form method="GET" action="{{ route('reports.show', $reportKey) }}" class="grid gap-3 border-b border-zinc-200 p-4 dark:border-zinc-700 md:grid-cols-4 xl:grid-cols-8">
                <select name="school_year_id" class="h-10 rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                    <option value="">{{ __('All school years') }}</option>
                    @foreach($schoolYears as $schoolYear)
                        <option value="{{ $schoolYear->id }}" @selected((int) request('school_year_id') === $schoolYear->id)>{{ $schoolYear->name }}</option>
                    @endforeach
                </select>

                <select name="grade_level_id" class="h-10 rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                    <option value="">{{ __('All grades') }}</option>
                    @foreach($gradeLevels as $gradeLevel)
                        <option value="{{ $gradeLevel->id }}" @selected((int) request('grade_level_id') === $gradeLevel->id)>{{ $gradeLevel->name }}</option>
                    @endforeach
                </select>

                <select name="section_id" class="h-10 rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                    <option value="">{{ __('All sections') }}</option>
                    @foreach($sections as $section)
                        <option value="{{ $section->id }}" @selected((int) request('section_id') === $section->id)>{{ $section->code }}</option>
                    @endforeach
                </select>

                <select name="teacher_id" class="h-10 rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                    <option value="">{{ __('All teachers') }}</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" @selected((int) request('teacher_id') === $teacher->id)>{{ $teacher->fullName() }}</option>
                    @endforeach
                </select>

                <select name="subject_id" class="h-10 rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                    <option value="">{{ __('All subjects') }}</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" @selected((int) request('subject_id') === $subject->id)>{{ $subject->name }}</option>
                    @endforeach
                </select>

                <select name="grading_period_id" class="h-10 rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                    <option value="">{{ __('All periods') }}</option>
                    @foreach($gradingPeriods as $period)
                        <option value="{{ $period->id }}" @selected((int) request('grading_period_id') === $period->id)>{{ $period->name }}</option>
                    @endforeach
                </select>

                <input name="date_from" type="date" value="{{ request('date_from') }}" class="h-10 rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                <input name="date_to" type="date" value="{{ request('date_to') }}" class="h-10 rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">

                <input name="status" value="{{ request('status') }}" placeholder="{{ __('Status') }}" class="h-10 rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950 md:col-span-2">
                <div class="flex gap-2 md:col-span-2 xl:col-span-6">
                    <flux:button type="submit">{{ __('Filter') }}</flux:button>
                    <flux:button :href="route('reports.show', $reportKey)" wire:navigate>{{ __('Clear') }}</flux:button>
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-zinc-200 text-xs uppercase text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
                        <tr>
                            @foreach($data['headings'] as $heading)
                                <th class="px-4 py-3">{{ $heading }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse($data['rows'] as $row)
                            <tr>
                                @foreach($row as $value)
                                    <td class="px-4 py-4">{{ filled($value) ? $value : __('Not set') }}</td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($data['headings']) }}" class="px-4 py-10 text-center text-zinc-500">
                                    {{ __('No report records found.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-layouts::app>
