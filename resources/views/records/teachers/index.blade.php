<x-layouts::app :title="__('Teachers')">
    <div class="mx-auto flex w-full max-w-7xl flex-1 flex-col gap-6 p-6">
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
            <div>
                <flux:heading size="xl">{{ __('Teachers') }}</flux:heading>
                <flux:subheading>{{ __('Manage teacher profiles, account links, and subject assignments.') }}</flux:subheading>
            </div>
            @can('teachers.create')
                <flux:button :href="route('records.teachers.create')" wire:navigate variant="primary">{{ __('Create Teacher') }}</flux:button>
            @endcan
        </div>

        <section class="rounded-lg border border-zinc-200 bg-white shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
            <form method="GET" action="{{ route('records.teachers.index') }}" class="grid gap-3 border-b border-zinc-200 p-4 dark:border-zinc-700 md:grid-cols-[1fr_180px_160px_160px_auto]">
                <input name="search" value="{{ request('search') }}" placeholder="{{ __('Search teachers') }}" class="h-10 rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                <select name="subject_id" class="h-10 rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                    <option value="">{{ __('All subjects') }}</option>
                    @foreach ($subjects as $subject)
                        <option value="{{ $subject->id }}" @selected((int) request('subject_id') === $subject->id)>{{ $subject->name }}</option>
                    @endforeach
                </select>
                <select name="account" class="h-10 rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                    <option value="">{{ __('All accounts') }}</option>
                    <option value="linked" @selected(request('account') === 'linked')>{{ __('Linked') }}</option>
                    <option value="unlinked" @selected(request('account') === 'unlinked')>{{ __('Unlinked') }}</option>
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
                            <th class="px-4 py-3">{{ __('Teacher') }}</th>
                            <th class="px-4 py-3">{{ __('Subjects') }}</th>
                            <th class="px-4 py-3">{{ __('Account') }}</th>
                            <th class="px-4 py-3">{{ __('Status') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse ($teachers as $teacher)
                            <tr>
                                <td class="px-4 py-4">
                                    <p class="font-medium text-zinc-950 dark:text-white">{{ $teacher->fullName() }}</p>
                                    <p class="text-zinc-500">{{ $teacher->employee_number }}</p>
                                </td>
                                <td class="px-4 py-4">
                                    {{ $teacher->subjects->pluck('code')->join(', ') ?: __('No subjects') }}
                                </td>
                                <td class="px-4 py-4">{{ $teacher->user?->email ?? __('Not linked') }}</td>
                                <td class="px-4 py-4"><x-records.partials.status-badge :status="$teacher->status" /></td>
                                <td class="px-4 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <flux:button size="sm" :href="route('records.teachers.show', $teacher)" wire:navigate>{{ __('View') }}</flux:button>
                                        @can('teachers.update')
                                            <flux:button size="sm" :href="route('records.teachers.edit', $teacher)" wire:navigate>{{ __('Edit') }}</flux:button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-10 text-center text-zinc-500">{{ __('No teachers found.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-zinc-200 p-4 dark:border-zinc-700">{{ $teachers->links() }}</div>
        </section>
    </div>
</x-layouts::app>
