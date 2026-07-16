<x-layouts::app :title="$config['title']">
    <div class="mx-auto flex w-full max-w-7xl flex-1 flex-col gap-6 p-6">
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
            <div>
                <flux:heading size="xl">{{ $config['title'] }}</flux:heading>
                <flux:subheading>{{ __('Manage academic setup records used by enrollment, schedules, attendance, and grades.') }}</flux:subheading>
            </div>

            @can('academic_setup.create')
                <flux:button :href="route($config['route'].'.create')" wire:navigate variant="primary">
                    {{ __('Create') }} {{ $config['singular'] }}
                </flux:button>
            @endcan
        </div>

        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        <section class="rounded-lg border border-zinc-200 bg-white shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
            <form method="GET" action="{{ route($config['route'].'.index') }}" class="grid gap-3 border-b border-zinc-200 p-4 dark:border-zinc-700 md:grid-cols-[1fr_180px_auto]">
                <input
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="{{ __('Search name or code') }}"
                    class="h-10 rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950"
                >

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
                            <th class="px-4 py-3 font-medium">{{ __('Name') }}</th>
                            <th class="px-4 py-3 font-medium">{{ __('Details') }}</th>
                            <th class="px-4 py-3 font-medium">{{ __('Status') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse ($records as $record)
                            <tr>
                                <td class="px-4 py-4">
                                    <p class="font-medium text-zinc-950 dark:text-white">{{ $record->name }}</p>
                                    @if (filled($record->code ?? null))
                                        <p class="text-zinc-500 dark:text-zinc-400">{{ $record->code }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-zinc-600 dark:text-zinc-300">
                                    @switch($resource)
                                        @case('school-years')
                                            <div>{{ $record->starts_at->toFormattedDateString() }} - {{ $record->ends_at->toFormattedDateString() }}</div>
                                            @if ($record->is_active)
                                                <span class="mt-1 inline-flex rounded-full bg-sky-100 px-2 py-1 text-xs font-medium text-sky-700 dark:bg-sky-500/15 dark:text-sky-300">{{ __('Active School Year') }}</span>
                                            @endif
                                            @break

                                        @case('terms')
                                            <div>{{ $record->schoolYear?->name }}</div>
                                            <div class="text-zinc-500 dark:text-zinc-400">{{ $record->starts_at->toFormattedDateString() }} - {{ $record->ends_at->toFormattedDateString() }}</div>
                                            @break

                                        @case('grade-levels')
                                            <div>{{ __('Sort order: :order', ['order' => $record->sort_order]) }}</div>
                                            @break

                                        @case('sections')
                                            <div>{{ $record->gradeLevel?->name }}</div>
                                            <div class="text-zinc-500 dark:text-zinc-400">{{ __('Capacity: :capacity', ['capacity' => $record->capacity ?? __('Not set')]) }}</div>
                                            @break

                                        @case('subjects')
                                            <div class="max-w-md truncate">{{ $record->description ?? __('No description') }}</div>
                                            @break

                                        @case('classrooms')
                                            <div>{{ $record->location ?? __('No location') }}</div>
                                            <div class="text-zinc-500 dark:text-zinc-400">{{ __('Capacity: :capacity', ['capacity' => $record->capacity ?? __('Not set')]) }}</div>
                                            @break
                                    @endswitch
                                </td>
                                <td class="px-4 py-4">
                                    <span @class([
                                        'rounded-full px-2 py-1 text-xs font-medium',
                                        'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' => $record->status === \App\Models\SchoolYear::STATUS_ACTIVE,
                                        'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300' => $record->status !== \App\Models\SchoolYear::STATUS_ACTIVE,
                                    ])>
                                        {{ str($record->status)->headline() }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-right">
                                    @can('academic_setup.update')
                                        <flux:button size="sm" :href="route($config['route'].'.edit', $record)" wire:navigate>
                                            {{ __('Edit') }}
                                        </flux:button>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-10 text-center text-zinc-500 dark:text-zinc-400">
                                    {{ __('No records found.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-zinc-200 p-4 dark:border-zinc-700">
                {{ $records->links() }}
            </div>
        </section>
    </div>
</x-layouts::app>
