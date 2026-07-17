<x-layouts::app :title="$classSchedule->subject->name">
    <div class="mx-auto flex w-full max-w-7xl flex-1 flex-col gap-6 p-6">
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
            <div>
                <flux:heading size="xl">{{ $classSchedule->subject->name }}</flux:heading>
                <flux:subheading>{{ $classSchedule->section->code }} · {{ str($classSchedule->day_of_week)->headline() }} · {{ substr($classSchedule->starts_at, 0, 5) }} - {{ substr($classSchedule->ends_at, 0, 5) }}</flux:subheading>
            </div>
            @can('schedules.update')
                <flux:button :href="route('scheduling.class-schedules.edit', $classSchedule)" wire:navigate variant="primary">{{ __('Edit Schedule') }}</flux:button>
            @endcan
        </div>

        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200">{{ session('status') }}</div>
        @endif

        <div class="grid gap-6 lg:grid-cols-[0.8fr_1.2fr]">
            <section class="rounded-lg border border-zinc-200 bg-white p-5 shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
                <h2 class="font-semibold">{{ __('Schedule') }}</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div><dt class="text-zinc-500">{{ __('Status') }}</dt><dd><x-records.partials.status-badge :status="$classSchedule->status" /></dd></div>
                    <div><dt class="text-zinc-500">{{ __('School Year') }}</dt><dd>{{ $classSchedule->schoolYear->name }}</dd></div>
                    <div><dt class="text-zinc-500">{{ __('Section') }}</dt><dd>{{ $classSchedule->section->gradeLevel?->name }} · {{ $classSchedule->section->code }}</dd></div>
                    <div><dt class="text-zinc-500">{{ __('Teacher') }}</dt><dd>{{ $classSchedule->teacher->fullName() }}</dd></div>
                    <div><dt class="text-zinc-500">{{ __('Classroom') }}</dt><dd>{{ $classSchedule->classroom?->name ?? __('No classroom') }}</dd></div>
                    <div><dt class="text-zinc-500">{{ __('Notes') }}</dt><dd>{{ $classSchedule->notes ?? __('No notes') }}</dd></div>
                </dl>
            </section>

            <section class="rounded-lg border border-zinc-200 bg-white p-5 shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
                <h2 class="font-semibold">{{ __('Conflict Context') }}</h2>
                <div class="mt-4 grid gap-4 text-sm md:grid-cols-3">
                    <div>
                        <p class="text-zinc-500">{{ __('Teacher') }}</p>
                        <p class="mt-1 font-medium">{{ $classSchedule->teacher->fullName() }}</p>
                    </div>
                    <div>
                        <p class="text-zinc-500">{{ __('Section') }}</p>
                        <p class="mt-1 font-medium">{{ $classSchedule->section->code }}</p>
                    </div>
                    <div>
                        <p class="text-zinc-500">{{ __('Room') }}</p>
                        <p class="mt-1 font-medium">{{ $classSchedule->classroom?->code ?? __('Unassigned') }}</p>
                    </div>
                </div>
                <p class="mt-5 text-sm text-zinc-500">{{ __('Active schedules are checked for overlapping teacher, section, and classroom assignments before saving.') }}</p>
            </section>
        </div>
    </div>
</x-layouts::app>
