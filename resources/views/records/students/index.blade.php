<x-layouts::app :title="__('Students')">
    <div class="mx-auto flex w-full max-w-7xl flex-1 flex-col gap-6 p-6">
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
            <div>
                <flux:heading size="xl">{{ __('Students') }}</flux:heading>
                <flux:subheading>{{ __('Manage student profiles, academic placement, and linked guardians.') }}</flux:subheading>
            </div>
            @can('students.create')
                <flux:button :href="route('records.students.create')" wire:navigate variant="primary">{{ __('Create Student') }}</flux:button>
            @endcan
        </div>

        <section class="rounded-lg border border-zinc-200 bg-white shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
            <form method="GET" action="{{ route('records.students.index') }}" class="grid gap-3 border-b border-zinc-200 p-4 dark:border-zinc-700 md:grid-cols-[1fr_180px_180px_160px_auto]">
                <input name="search" value="{{ request('search') }}" placeholder="{{ __('Search students') }}" class="h-10 rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                <select name="grade_level_id" class="h-10 rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                    <option value="">{{ __('All grades') }}</option>
                    @foreach ($gradeLevels as $gradeLevel)
                        <option value="{{ $gradeLevel->id }}" @selected((int) request('grade_level_id') === $gradeLevel->id)>{{ $gradeLevel->name }}</option>
                    @endforeach
                </select>
                <select name="section_id" class="h-10 rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                    <option value="">{{ __('All sections') }}</option>
                    @foreach ($sections as $section)
                        <option value="{{ $section->id }}" @selected((int) request('section_id') === $section->id)>{{ $section->code }}</option>
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
                            <th class="px-4 py-3">{{ __('Student') }}</th>
                            <th class="px-4 py-3">{{ __('Placement') }}</th>
                            <th class="px-4 py-3">{{ __('Guardians') }}</th>
                            <th class="px-4 py-3">{{ __('Status') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse ($students as $student)
                            <tr>
                                <td class="px-4 py-4">
                                    <p class="font-medium text-zinc-950 dark:text-white">{{ $student->fullName() }}</p>
                                    <p class="text-zinc-500">{{ $student->student_number }}</p>
                                </td>
                                <td class="px-4 py-4">{{ $student->gradeLevel?->name ?? __('Not set') }} / {{ $student->section?->code ?? __('No section') }}</td>
                                <td class="px-4 py-4">{{ $student->guardians->count() }}</td>
                                <td class="px-4 py-4"><x-records.partials.status-badge :status="$student->status" /></td>
                                <td class="px-4 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <flux:button size="sm" :href="route('records.students.show', $student)" wire:navigate>{{ __('View') }}</flux:button>
                                        @can('students.update')
                                            <flux:button size="sm" :href="route('records.students.edit', $student)" wire:navigate>{{ __('Edit') }}</flux:button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-10 text-center text-zinc-500">{{ __('No students found.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-zinc-200 p-4 dark:border-zinc-700">{{ $students->links() }}</div>
        </section>
    </div>
</x-layouts::app>
