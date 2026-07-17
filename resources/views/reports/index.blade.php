<x-layouts::app :title="__('Reports')">
    <div class="mx-auto flex w-full max-w-7xl flex-1 flex-col gap-6 p-6">
        <div>
            <flux:heading size="xl">{{ __('Reports') }}</flux:heading>
            <flux:subheading>{{ __('Review academic operations through read-only summaries and formal exports.') }}</flux:subheading>
        </div>

        <section class="rounded-lg border border-zinc-200 bg-white shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
            <div class="grid divide-y divide-zinc-200 dark:divide-zinc-800">
                @foreach($reports as $key => $title)
                    <div class="flex flex-col justify-between gap-4 p-5 md:flex-row md:items-center">
                        <div>
                            <p class="font-medium text-zinc-950 dark:text-white">{{ $title }}</p>
                            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                                {{ __('Preview filtered records and export a controlled report file.') }}
                            </p>
                        </div>

                        <flux:button size="sm" :href="route('reports.show', $key)" wire:navigate>
                            {{ __('Open Report') }}
                        </flux:button>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
</x-layouts::app>
