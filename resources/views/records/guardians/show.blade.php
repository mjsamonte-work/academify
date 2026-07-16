<x-layouts::app :title="$guardian->fullName()">
    <div class="mx-auto flex w-full max-w-7xl flex-1 flex-col gap-6 p-6">
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
            <div>
                <flux:heading size="xl">{{ $guardian->fullName() }}</flux:heading>
                <flux:subheading>{{ $guardian->email ?? __('No email') }}</flux:subheading>
            </div>
            @can('guardians.update')
                <flux:button :href="route('records.guardians.edit', $guardian)" wire:navigate variant="primary">{{ __('Edit Guardian') }}</flux:button>
            @endcan
        </div>
        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200">{{ session('status') }}</div>
        @endif
        <div class="grid gap-6 lg:grid-cols-[0.8fr_1.2fr]">
            <section class="rounded-lg border border-zinc-200 bg-white p-5 shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
                <h2 class="font-semibold">{{ __('Profile') }}</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div><dt class="text-zinc-500">{{ __('Status') }}</dt><dd><x-records.partials.status-badge :status="$guardian->status" /></dd></div>
                    <div><dt class="text-zinc-500">{{ __('Phone') }}</dt><dd>{{ $guardian->phone ?? __('No phone') }}</dd></div>
                    <div><dt class="text-zinc-500">{{ __('Alternate Phone') }}</dt><dd>{{ $guardian->alternate_phone ?? __('Not provided') }}</dd></div>
                    <div><dt class="text-zinc-500">{{ __('Occupation') }}</dt><dd>{{ $guardian->occupation ?? __('Not provided') }}</dd></div>
                    <div><dt class="text-zinc-500">{{ __('Address') }}</dt><dd>{{ $guardian->address ?? __('Not provided') }}</dd></div>
                </dl>
            </section>
            <section class="rounded-lg border border-zinc-200 bg-white shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
                <div class="border-b border-zinc-200 px-5 py-4 dark:border-zinc-700"><h2 class="font-semibold">{{ __('Linked Students') }}</h2></div>
                <div class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse ($guardian->students as $student)
                        <div class="p-5">
                            <p class="font-medium">{{ $student->fullName() }}</p>
                            <p class="text-sm text-zinc-500">{{ $student->student_number }} · {{ $student->pivot->relationship }}</p>
                        </div>
                    @empty
                        <p class="p-5 text-sm text-zinc-500">{{ __('No students linked yet.') }}</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-layouts::app>
