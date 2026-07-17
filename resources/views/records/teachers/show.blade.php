<x-layouts::app :title="$teacher->fullName()">
    <div class="mx-auto flex w-full max-w-7xl flex-1 flex-col gap-6 p-6">
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
            <div>
                <flux:heading size="xl">{{ $teacher->fullName() }}</flux:heading>
                <flux:subheading>{{ $teacher->employee_number }} · {{ $teacher->job_title ?? __('Teacher') }}</flux:subheading>
            </div>
            @can('teachers.update')
                <flux:button :href="route('records.teachers.edit', $teacher)" wire:navigate variant="primary">{{ __('Edit Teacher') }}</flux:button>
            @endcan
        </div>

        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200">{{ session('status') }}</div>
        @endif

        <div class="grid gap-6 lg:grid-cols-[0.9fr_1.1fr]">
            <section class="rounded-lg border border-zinc-200 bg-white p-5 shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
                <h2 class="font-semibold">{{ __('Profile') }}</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div><dt class="text-zinc-500">{{ __('Status') }}</dt><dd><x-records.partials.status-badge :status="$teacher->status" /></dd></div>
                    <div><dt class="text-zinc-500">{{ __('Department') }}</dt><dd>{{ $teacher->department ?? __('Not provided') }}</dd></div>
                    <div><dt class="text-zinc-500">{{ __('Employment Type') }}</dt><dd>{{ $teacher->employment_type ? str($teacher->employment_type)->replace('_', ' ')->headline() : __('Not provided') }}</dd></div>
                    <div><dt class="text-zinc-500">{{ __('Hired Date') }}</dt><dd>{{ $teacher->hired_at?->toFormattedDateString() ?? __('Not provided') }}</dd></div>
                    <div><dt class="text-zinc-500">{{ __('Contact') }}</dt><dd>{{ $teacher->email ?? __('No email') }} · {{ $teacher->phone ?? __('No phone') }}</dd></div>
                    <div><dt class="text-zinc-500">{{ __('Address') }}</dt><dd>{{ $teacher->address ?? __('Not provided') }}</dd></div>
                </dl>
            </section>

            <section class="rounded-lg border border-zinc-200 bg-white shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
                <div class="border-b border-zinc-200 px-5 py-4 dark:border-zinc-700"><h2 class="font-semibold">{{ __('Account And Subjects') }}</h2></div>
                <div class="grid gap-5 p-5 md:grid-cols-2">
                    <div>
                        <p class="text-sm text-zinc-500">{{ __('Linked Account') }}</p>
                        <p class="mt-1 font-medium">{{ $teacher->user?->name ?? __('Not linked') }}</p>
                        <p class="text-sm text-zinc-500">{{ $teacher->user?->email }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-zinc-500">{{ __('Assigned Subjects') }}</p>
                        <p class="mt-1 font-medium">{{ $teacher->subjects->count() }}</p>
                    </div>
                </div>
                <div class="divide-y divide-zinc-200 border-t border-zinc-200 dark:divide-zinc-800 dark:border-zinc-700">
                    @forelse ($teacher->subjects as $subject)
                        <div class="flex items-center justify-between gap-4 p-5">
                            <div>
                                <p class="font-medium">{{ $subject->name }}</p>
                                <p class="text-sm text-zinc-500">{{ $subject->code }}</p>
                            </div>
                            <x-records.partials.status-badge :status="$subject->status" />
                        </div>
                    @empty
                        <p class="p-5 text-sm text-zinc-500">{{ __('No subjects assigned yet.') }}</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-layouts::app>
