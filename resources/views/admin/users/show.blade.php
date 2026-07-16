<x-layouts::app :title="$user->name">
    <div class="mx-auto flex w-full max-w-7xl flex-1 flex-col gap-6 p-6">
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
            <div>
                <flux:heading size="xl">{{ $user->name }}</flux:heading>
                <flux:subheading>{{ $user->email }}</flux:subheading>
            </div>

            @can('users.update')
                <flux:button :href="route('admin.users.edit', $user)" wire:navigate variant="primary">
                    {{ __('Edit User') }}
                </flux:button>
            @endcan
        </div>

        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid gap-6 lg:grid-cols-[0.8fr_1.2fr]">
            <section class="rounded-lg border border-zinc-200 bg-white p-5 shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
                <h2 class="text-base font-semibold text-zinc-950 dark:text-white">{{ __('Account Information') }}</h2>
                <dl class="mt-5 space-y-4 text-sm">
                    <div>
                        <dt class="text-zinc-500 dark:text-zinc-400">{{ __('Status') }}</dt>
                        <dd class="mt-1 font-medium">{{ str($user->status)->headline() }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-500 dark:text-zinc-400">{{ __('Phone') }}</dt>
                        <dd class="mt-1 font-medium">{{ $user->phone ?: __('Not provided') }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-500 dark:text-zinc-400">{{ __('Job Title') }}</dt>
                        <dd class="mt-1 font-medium">{{ $user->job_title ?: __('Not provided') }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-500 dark:text-zinc-400">{{ __('Roles') }}</dt>
                        <dd class="mt-2 flex flex-wrap gap-2">
                            @foreach ($user->roles as $role)
                                <span class="rounded-full bg-zinc-100 px-2 py-1 text-xs font-medium text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">{{ $role->name }}</span>
                            @endforeach
                        </dd>
                    </div>
                </dl>
            </section>

            <section class="rounded-lg border border-zinc-200 bg-white shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
                <div class="border-b border-zinc-200 px-5 py-4 dark:border-zinc-700">
                    <h2 class="text-base font-semibold text-zinc-950 dark:text-white">{{ __('Recent Login Logs') }}</h2>
                </div>
                <div class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse ($user->loginLogs as $log)
                        <div class="grid gap-2 px-5 py-4 text-sm md:grid-cols-[1fr_1fr_auto]">
                            <span>{{ $log->ip_address ?? __('Unknown IP') }}</span>
                            <span class="truncate text-zinc-500 dark:text-zinc-400">{{ $log->user_agent ?? __('Unknown device') }}</span>
                            <span class="text-zinc-500 dark:text-zinc-400">{{ $log->logged_in_at->diffForHumans() }}</span>
                        </div>
                    @empty
                        <p class="px-5 py-8 text-sm text-zinc-500 dark:text-zinc-400">{{ __('No login activity has been recorded for this account.') }}</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-layouts::app>
