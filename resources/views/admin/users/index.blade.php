<x-layouts::app :title="__('Users')">
    <div class="mx-auto flex w-full max-w-7xl flex-1 flex-col gap-6 p-6">
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
            <div>
                <flux:heading size="xl">{{ __('Users') }}</flux:heading>
                <flux:subheading>{{ __('Manage Academify accounts, statuses, and assigned roles.') }}</flux:subheading>
            </div>

            @can('users.create')
                <flux:button :href="route('admin.users.create')" wire:navigate variant="primary">
                    {{ __('Create User') }}
                </flux:button>
            @endcan
        </div>

        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        <section class="rounded-lg border border-zinc-200 bg-white shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
            <form method="GET" action="{{ route('admin.users.index') }}" class="grid gap-3 border-b border-zinc-200 p-4 dark:border-zinc-700 md:grid-cols-[1fr_220px_180px_auto]">
                <input
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="{{ __('Search name or email') }}"
                    class="h-10 rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950"
                >

                <select name="role" class="h-10 rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                    <option value="">{{ __('All roles') }}</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role }}" @selected(request('role') === $role)>{{ $role }}</option>
                    @endforeach
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
                            <th class="px-4 py-3 font-medium">{{ __('User') }}</th>
                            <th class="px-4 py-3 font-medium">{{ __('Roles') }}</th>
                            <th class="px-4 py-3 font-medium">{{ __('Status') }}</th>
                            <th class="px-4 py-3 font-medium">{{ __('Latest Login') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse ($users as $user)
                            <tr>
                                <td class="px-4 py-4">
                                    <p class="font-medium text-zinc-950 dark:text-white">{{ $user->name }}</p>
                                    <p class="text-zinc-500 dark:text-zinc-400">{{ $user->email }}</p>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($user->roles as $role)
                                            <span class="rounded-full bg-zinc-100 px-2 py-1 text-xs font-medium text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">{{ $role->name }}</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <span @class([
                                        'rounded-full px-2 py-1 text-xs font-medium',
                                        'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' => $user->status === \App\Models\User::STATUS_ACTIVE,
                                        'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300' => $user->status !== \App\Models\User::STATUS_ACTIVE,
                                    ])>
                                        {{ str($user->status)->headline() }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-zinc-500 dark:text-zinc-400">
                                    {{ $user->login_logs_max_logged_in_at ? \Illuminate\Support\Carbon::parse($user->login_logs_max_logged_in_at)->diffForHumans() : __('Never') }}
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <flux:button size="sm" :href="route('admin.users.show', $user)" wire:navigate>{{ __('View') }}</flux:button>
                                        @can('users.update')
                                            <flux:button size="sm" :href="route('admin.users.edit', $user)" wire:navigate>{{ __('Edit') }}</flux:button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-zinc-500 dark:text-zinc-400">
                                    {{ __('No users found.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-zinc-200 p-4 dark:border-zinc-700">
                {{ $users->links() }}
            </div>
        </section>
    </div>
</x-layouts::app>
