<x-layouts::app :title="__('Notices')">
    <div class="mx-auto flex w-full max-w-7xl flex-1 flex-col gap-6 p-6">
        <div><flux:heading size="xl">{{ __('Notices') }}</flux:heading><flux:subheading>{{ __('View announcements intended for your account.') }}</flux:subheading></div>
        <section class="rounded-lg border border-zinc-200 bg-white shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
            <div class="overflow-x-auto"><table class="w-full text-left text-sm"><thead class="border-b border-zinc-200 text-xs uppercase text-zinc-500 dark:border-zinc-700 dark:text-zinc-400"><tr><th class="px-4 py-3">{{ __('Notice') }}</th><th class="px-4 py-3">{{ __('Priority') }}</th><th class="px-4 py-3">{{ __('Read') }}</th><th class="px-4 py-3 text-right">{{ __('Actions') }}</th></tr></thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">@forelse($notifications as $notification)<tr><td class="px-4 py-4"><p class="font-medium">{{ $notification->announcement->title }}</p><p class="text-zinc-500">{{ $notification->announcement->publish_at?->toFormattedDateString() ?? __('No publish date') }}</p></td><td class="px-4 py-4">{{ str($notification->announcement->priority)->headline() }}</td><td class="px-4 py-4">{{ $notification->read_at ? __('Read') : __('Unread') }}</td><td class="px-4 py-4 text-right"><flux:button size="sm" :href="route('notices.show', $notification)" wire:navigate>{{ __('View') }}</flux:button></td></tr>@empty<tr><td colspan="4" class="px-4 py-10 text-center text-zinc-500">{{ __('No notices found.') }}</td></tr>@endforelse</tbody>
            </table></div><div class="border-t border-zinc-200 p-4 dark:border-zinc-700">{{ $notifications->links() }}</div>
        </section>
    </div>
</x-layouts::app>
