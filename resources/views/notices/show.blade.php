<x-layouts::app :title="$notification->announcement->title">
    <div class="mx-auto flex w-full max-w-5xl flex-1 flex-col gap-6 p-6">
        <div><flux:heading size="xl">{{ $notification->announcement->title }}</flux:heading><flux:subheading>{{ str($notification->announcement->priority)->headline() }} · {{ $notification->announcement->creator->name }}</flux:subheading></div>
        @if(session('status'))<div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>@endif
        <section class="rounded-lg border border-zinc-200 bg-white p-5 shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
            <div class="whitespace-pre-line text-sm">{{ $notification->announcement->body }}</div>
            <div class="mt-6 flex items-center justify-between border-t border-zinc-200 pt-4 dark:border-zinc-700">
                <p class="text-sm text-zinc-500">{{ $notification->read_at ? __('Read') : __('Unread') }}</p>
                @if(! $notification->read_at)<form method="POST" action="{{ route('notices.read', $notification) }}">@csrf<flux:button type="submit" variant="primary">{{ __('Mark As Read') }}</flux:button></form>@endif
            </div>
        </section>
    </div>
</x-layouts::app>
