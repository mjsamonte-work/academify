<x-layouts::app :title="__('Dashboard')">
    <div class="mx-auto flex w-full max-w-7xl flex-1 flex-col gap-6 p-6">
        <div class="flex flex-col gap-2">
            <flux:heading size="xl">{{ __('Dashboard') }}</flux:heading>
            <flux:subheading>{{ __('Academify setup overview and account administration status.') }}</flux:subheading>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <section class="rounded-lg border border-zinc-200 bg-white p-5 shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Total Users') }}</p>
                <p class="mt-2 text-3xl font-semibold text-zinc-950 dark:text-white">{{ \App\Models\User::count() }}</p>
            </section>

            <section class="rounded-lg border border-zinc-200 bg-white p-5 shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Active Users') }}</p>
                <p class="mt-2 text-3xl font-semibold text-zinc-950 dark:text-white">{{ \App\Models\User::where('status', \App\Models\User::STATUS_ACTIVE)->count() }}</p>
            </section>

            <section class="rounded-lg border border-zinc-200 bg-white p-5 shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Roles Configured') }}</p>
                <p class="mt-2 text-3xl font-semibold text-zinc-950 dark:text-white">{{ \Spatie\Permission\Models\Role::count() }}</p>
            </section>
        </div>

        <div class="grid gap-6 lg:grid-cols-[1.3fr_0.7fr]">
            <section class="rounded-lg border border-zinc-200 bg-white shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
                <div class="border-b border-zinc-200 px-5 py-4 dark:border-zinc-700">
                    <h2 class="text-base font-semibold text-zinc-950 dark:text-white">{{ __('Recent Login Activity') }}</h2>
                </div>

                <div class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse (\App\Models\LoginLog::query()->with('user')->latest('logged_in_at')->limit(5)->get() as $log)
                        <div class="flex items-center justify-between gap-4 px-5 py-4">
                            <div>
                                <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $log->user?->name }}</p>
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $log->ip_address ?? __('Unknown IP') }}</p>
                            </div>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $log->logged_in_at->diffForHumans() }}</p>
                        </div>
                    @empty
                        <p class="px-5 py-8 text-sm text-zinc-500 dark:text-zinc-400">{{ __('No login activity has been recorded yet.') }}</p>
                    @endforelse
                </div>
            </section>

            <section class="rounded-lg border border-zinc-200 bg-white p-5 shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
                <h2 class="text-base font-semibold text-zinc-950 dark:text-white">{{ __('Initial Setup') }}</h2>
                <div class="mt-4 space-y-3 text-sm text-zinc-600 dark:text-zinc-300">
                    <div class="flex items-center justify-between gap-3">
                        <span>{{ __('Academify branding') }}</span>
                        <span class="rounded-full bg-emerald-100 px-2 py-1 text-xs font-medium text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300">{{ __('Ready') }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <span>{{ __('Roles and permissions') }}</span>
                        <span class="rounded-full bg-emerald-100 px-2 py-1 text-xs font-medium text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300">{{ __('Seeded') }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <span>{{ __('User management') }}</span>
                        <span class="rounded-full bg-sky-100 px-2 py-1 text-xs font-medium text-sky-700 dark:bg-sky-500/15 dark:text-sky-300">{{ __('Active') }}</span>
                    </div>
                </div>

                @can('users.view')
                    <div class="mt-6">
                        <flux:button :href="route('admin.users.index')" wire:navigate variant="primary" class="w-full">
                            {{ __('Manage Users') }}
                        </flux:button>
                    </div>
                @endcan
            </section>
        </div>
    </div>
</x-layouts::app>
