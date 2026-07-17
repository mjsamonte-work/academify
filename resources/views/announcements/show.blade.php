<x-layouts::app :title="$announcement->title">
    <div class="mx-auto flex w-full max-w-7xl flex-1 flex-col gap-6 p-6">
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center"><div><flux:heading size="xl">{{ $announcement->title }}</flux:heading><flux:subheading>{{ str($announcement->priority)->headline() }} · {{ $announcement->creator->name }}</flux:subheading></div>@can('announcements.update')<flux:button :href="route('announcements.edit', $announcement)" wire:navigate variant="primary">{{ __('Edit Announcement') }}</flux:button>@endcan</div>
        @if(session('status'))<div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>@endif
        <div class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
            <section class="rounded-lg border border-zinc-200 bg-white p-5 shadow-xs dark:border-zinc-700 dark:bg-zinc-900"><x-records.partials.status-badge :status="$announcement->status" /><div class="mt-4 whitespace-pre-line text-sm">{{ $announcement->body }}</div></section>
            <section class="rounded-lg border border-zinc-200 bg-white p-5 shadow-xs dark:border-zinc-700 dark:bg-zinc-900"><h2 class="font-semibold">{{ __('Delivery') }}</h2><dl class="mt-4 space-y-3 text-sm"><div><dt class="text-zinc-500">{{ __('Audience Rules') }}</dt><dd>{{ $announcement->audiences->count() }}</dd></div><div><dt class="text-zinc-500">{{ __('Notifications') }}</dt><dd>{{ $announcement->notifications->count() }}</dd></div><div><dt class="text-zinc-500">{{ __('Publish') }}</dt><dd>{{ $announcement->publish_at?->toFormattedDateString() ?? __('Not set') }}</dd></div></dl></section>
        </div>
    </div>
</x-layouts::app>
