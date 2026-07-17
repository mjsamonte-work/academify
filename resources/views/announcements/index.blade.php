<x-layouts::app :title="__('Announcements')">
    <div class="mx-auto flex w-full max-w-7xl flex-1 flex-col gap-6 p-6">
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
            <div><flux:heading size="xl">{{ __('Announcements') }}</flux:heading><flux:subheading>{{ __('Manage formal notices and audience delivery.') }}</flux:subheading></div>
            @can('announcements.create')<flux:button :href="route('announcements.create')" wire:navigate variant="primary">{{ __('Create Announcement') }}</flux:button>@endcan
        </div>
        <section class="rounded-lg border border-zinc-200 bg-white shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
            <form method="GET" action="{{ route('announcements.index') }}" class="grid gap-3 border-b border-zinc-200 p-4 dark:border-zinc-700 md:grid-cols-[1fr_160px_160px_auto]">
                <div></div>
                <select name="status" class="h-10 rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950"><option value="">{{ __('All statuses') }}</option>@foreach($statuses as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ str($status)->headline() }}</option>@endforeach</select>
                <select name="priority" class="h-10 rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950"><option value="">{{ __('All priorities') }}</option>@foreach($priorities as $priority)<option value="{{ $priority }}" @selected(request('priority') === $priority)>{{ str($priority)->headline() }}</option>@endforeach</select>
                <flux:button type="submit">{{ __('Filter') }}</flux:button>
            </form>
            <div class="overflow-x-auto"><table class="w-full text-left text-sm"><thead class="border-b border-zinc-200 text-xs uppercase text-zinc-500 dark:border-zinc-700 dark:text-zinc-400"><tr><th class="px-4 py-3">{{ __('Announcement') }}</th><th class="px-4 py-3">{{ __('Audience') }}</th><th class="px-4 py-3">{{ __('Status') }}</th><th class="px-4 py-3 text-right">{{ __('Actions') }}</th></tr></thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">@forelse($announcements as $announcement)<tr><td class="px-4 py-4"><p class="font-medium">{{ $announcement->title }}</p><p class="text-zinc-500">{{ str($announcement->priority)->headline() }} · {{ $announcement->creator->name }}</p></td><td class="px-4 py-4">{{ $announcement->audiences->pluck('audience_type')->map(fn($type) => str($type)->headline())->join(', ') ?: __('No audience') }}</td><td class="px-4 py-4"><x-records.partials.status-badge :status="$announcement->status" /></td><td class="px-4 py-4 text-right"><div class="flex justify-end gap-2"><flux:button size="sm" :href="route('announcements.show', $announcement)" wire:navigate>{{ __('View') }}</flux:button>@can('announcements.update')<flux:button size="sm" :href="route('announcements.edit', $announcement)" wire:navigate>{{ __('Edit') }}</flux:button>@endcan</div></td></tr>@empty<tr><td colspan="4" class="px-4 py-10 text-center text-zinc-500">{{ __('No announcements found.') }}</td></tr>@endforelse</tbody>
            </table></div><div class="border-t border-zinc-200 p-4 dark:border-zinc-700">{{ $announcements->links() }}</div>
        </section>
    </div>
</x-layouts::app>
