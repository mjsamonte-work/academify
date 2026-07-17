<x-layouts::app :title="__('Audit Logs')">
    <div class="mx-auto flex w-full max-w-7xl flex-1 flex-col gap-6 p-6">
        <div>
            <flux:heading size="xl">{{ __('Audit Logs') }}</flux:heading>
            <flux:subheading>{{ __('Review important changes made across Academify records.') }}</flux:subheading>
        </div>

        <section class="rounded-lg border border-zinc-200 bg-white shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
            <form method="GET" action="{{ route('administration.audit-logs.index') }}" class="grid gap-3 border-b border-zinc-200 p-4 dark:border-zinc-700 md:grid-cols-4 xl:grid-cols-8">
                <input name="search" value="{{ request('search') }}" placeholder="{{ __('Record or type') }}" class="h-10 rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950 md:col-span-2">

                <select name="module" class="h-10 rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                    <option value="">{{ __('All modules') }}</option>
                    @foreach($modules as $module)
                        <option value="{{ $module }}" @selected(request('module') === $module)>{{ str($module)->headline() }}</option>
                    @endforeach
                </select>

                <select name="action" class="h-10 rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                    <option value="">{{ __('All actions') }}</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" @selected(request('action') === $action)>{{ str($action)->headline() }}</option>
                    @endforeach
                </select>

                <select name="user_id" class="h-10 rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                    <option value="">{{ __('All users') }}</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @selected((int) request('user_id') === $user->id)>{{ $user->name }}</option>
                    @endforeach
                </select>

                <input name="date_from" type="date" value="{{ request('date_from') }}" class="h-10 rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                <input name="date_to" type="date" value="{{ request('date_to') }}" class="h-10 rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">

                <div class="flex gap-2">
                    <flux:button type="submit">{{ __('Filter') }}</flux:button>
                    <flux:button :href="route('administration.audit-logs.index')" wire:navigate>{{ __('Clear') }}</flux:button>
                </div>
            </form>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-zinc-200 text-xs uppercase text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
                        <tr>
                            <th class="px-4 py-3">{{ __('Activity') }}</th>
                            <th class="px-4 py-3">{{ __('Record') }}</th>
                            <th class="px-4 py-3">{{ __('User') }}</th>
                            <th class="px-4 py-3">{{ __('Date') }}</th>
                            <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse($activityLogs as $activityLog)
                            <tr>
                                <td class="px-4 py-4">
                                    <p class="font-medium">{{ str($activityLog->action)->headline() }}</p>
                                    <p class="text-zinc-500">{{ str($activityLog->module)->headline() }}</p>
                                </td>
                                <td class="px-4 py-4">
                                    <p>{{ $activityLog->subject_label ?? __('Unknown record') }}</p>
                                    <p class="text-xs text-zinc-500">{{ str($activityLog->subject_type)->classBasename() }}</p>
                                </td>
                                <td class="px-4 py-4">{{ $activityLog->user?->name ?? __('System') }}</td>
                                <td class="px-4 py-4">{{ $activityLog->created_at?->format('M d, Y H:i') }}</td>
                                <td class="px-4 py-4 text-right">
                                    <flux:button size="sm" :href="route('administration.audit-logs.show', $activityLog)" wire:navigate>
                                        {{ __('View') }}
                                    </flux:button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-zinc-500">{{ __('No audit logs found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-zinc-200 p-4 dark:border-zinc-700">{{ $activityLogs->links() }}</div>
        </section>
    </div>
</x-layouts::app>
