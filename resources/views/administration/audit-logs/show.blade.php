<x-layouts::app :title="__('Audit Log')">
    <div class="mx-auto flex w-full max-w-5xl flex-1 flex-col gap-6 p-6">
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
            <div>
                <flux:heading size="xl">{{ __('Audit Log') }}</flux:heading>
                <flux:subheading>{{ $activityLog->subject_label ?? __('Recorded activity') }}</flux:subheading>
            </div>
            <flux:button :href="route('administration.audit-logs.index')" wire:navigate>{{ __('Back to Logs') }}</flux:button>
        </div>

        <section class="rounded-lg border border-zinc-200 bg-white p-5 shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
            <dl class="grid gap-4 text-sm md:grid-cols-2">
                <div><dt class="text-zinc-500">{{ __('Action') }}</dt><dd class="mt-1 font-medium">{{ str($activityLog->action)->headline() }}</dd></div>
                <div><dt class="text-zinc-500">{{ __('Module') }}</dt><dd class="mt-1 font-medium">{{ str($activityLog->module)->headline() }}</dd></div>
                <div><dt class="text-zinc-500">{{ __('User') }}</dt><dd class="mt-1 font-medium">{{ $activityLog->user?->name ?? __('System') }}</dd></div>
                <div><dt class="text-zinc-500">{{ __('Recorded') }}</dt><dd class="mt-1 font-medium">{{ $activityLog->created_at?->format('M d, Y H:i') }}</dd></div>
                <div><dt class="text-zinc-500">{{ __('IP Address') }}</dt><dd class="mt-1 font-medium">{{ $activityLog->ip_address ?? __('Not available') }}</dd></div>
                <div><dt class="text-zinc-500">{{ __('Record Type') }}</dt><dd class="mt-1 font-medium">{{ str($activityLog->subject_type)->classBasename() }}</dd></div>
            </dl>
        </section>

        <section class="grid gap-6 md:grid-cols-2">
            <div class="rounded-lg border border-zinc-200 bg-white shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
                <div class="border-b border-zinc-200 p-4 dark:border-zinc-700">
                    <p class="font-medium">{{ __('Previous Values') }}</p>
                </div>
                <pre class="overflow-x-auto p-4 text-xs">{{ json_encode($activityLog->old_values ?? [], JSON_PRETTY_PRINT) }}</pre>
            </div>

            <div class="rounded-lg border border-zinc-200 bg-white shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
                <div class="border-b border-zinc-200 p-4 dark:border-zinc-700">
                    <p class="font-medium">{{ __('New Values') }}</p>
                </div>
                <pre class="overflow-x-auto p-4 text-xs">{{ json_encode($activityLog->new_values ?? [], JSON_PRETTY_PRINT) }}</pre>
            </div>
        </section>
    </div>
</x-layouts::app>
