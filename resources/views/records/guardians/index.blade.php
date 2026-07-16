<x-layouts::app :title="__('Guardians')">
    <div class="mx-auto flex w-full max-w-7xl flex-1 flex-col gap-6 p-6">
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
            <div>
                <flux:heading size="xl">{{ __('Guardians') }}</flux:heading>
                <flux:subheading>{{ __('Manage guardian profiles and linked students.') }}</flux:subheading>
            </div>
            @can('guardians.create')
                <flux:button :href="route('records.guardians.create')" wire:navigate variant="primary">{{ __('Create Guardian') }}</flux:button>
            @endcan
        </div>
        <section class="rounded-lg border border-zinc-200 bg-white shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
            <form method="GET" action="{{ route('records.guardians.index') }}" class="grid gap-3 border-b border-zinc-200 p-4 dark:border-zinc-700 md:grid-cols-[1fr_180px_auto]">
                <input name="search" value="{{ request('search') }}" placeholder="{{ __('Search guardians') }}" class="h-10 rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
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
                            <th class="px-4 py-3">{{ __('Guardian') }}</th>
                            <th class="px-4 py-3">{{ __('Contact') }}</th>
                            <th class="px-4 py-3">{{ __('Students') }}</th>
                            <th class="px-4 py-3">{{ __('Status') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse ($guardians as $guardian)
                            <tr>
                                <td class="px-4 py-4"><p class="font-medium">{{ $guardian->fullName() }}</p><p class="text-zinc-500">{{ $guardian->occupation ?? __('No occupation') }}</p></td>
                                <td class="px-4 py-4">{{ $guardian->email ?? __('No email') }}<br><span class="text-zinc-500">{{ $guardian->phone ?? __('No phone') }}</span></td>
                                <td class="px-4 py-4">{{ $guardian->students->count() }}</td>
                                <td class="px-4 py-4"><x-records.partials.status-badge :status="$guardian->status" /></td>
                                <td class="px-4 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <flux:button size="sm" :href="route('records.guardians.show', $guardian)" wire:navigate>{{ __('View') }}</flux:button>
                                        @can('guardians.update')
                                            <flux:button size="sm" :href="route('records.guardians.edit', $guardian)" wire:navigate>{{ __('Edit') }}</flux:button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-10 text-center text-zinc-500">{{ __('No guardians found.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-zinc-200 p-4 dark:border-zinc-700">{{ $guardians->links() }}</div>
        </section>
    </div>
</x-layouts::app>
