<x-layouts::app :title="__('My Teacher Profile')">
    <div class="mx-auto flex w-full max-w-6xl flex-1 flex-col gap-6 p-6">
        <div>
            <flux:heading size="xl">{{ __('My Teacher Profile') }}</flux:heading>
            <flux:subheading>{{ __('View your Academify teacher record and assigned subjects.') }}</flux:subheading>
        </div>

        @if ($teacher)
            <div class="grid gap-6 lg:grid-cols-[0.9fr_1.1fr]">
                <section class="rounded-lg border border-zinc-200 bg-white p-5 shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
                    <h2 class="font-semibold">{{ $teacher->fullName() }}</h2>
                    <dl class="mt-4 space-y-3 text-sm">
                        <div><dt class="text-zinc-500">{{ __('Employee Number') }}</dt><dd>{{ $teacher->employee_number }}</dd></div>
                        <div><dt class="text-zinc-500">{{ __('Status') }}</dt><dd><x-records.partials.status-badge :status="$teacher->status" /></dd></div>
                        <div><dt class="text-zinc-500">{{ __('Job Title') }}</dt><dd>{{ $teacher->job_title ?? __('Teacher') }}</dd></div>
                        <div><dt class="text-zinc-500">{{ __('Department') }}</dt><dd>{{ $teacher->department ?? __('Not provided') }}</dd></div>
                        <div><dt class="text-zinc-500">{{ __('Contact') }}</dt><dd>{{ $teacher->email ?? __('No email') }} · {{ $teacher->phone ?? __('No phone') }}</dd></div>
                    </dl>
                </section>

                <section class="rounded-lg border border-zinc-200 bg-white shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="border-b border-zinc-200 px-5 py-4 dark:border-zinc-700"><h2 class="font-semibold">{{ __('Assigned Subjects') }}</h2></div>
                    <div class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse ($teacher->subjects as $subject)
                            <div class="p-5">
                                <p class="font-medium">{{ $subject->name }}</p>
                                <p class="text-sm text-zinc-500">{{ $subject->code }}</p>
                            </div>
                        @empty
                            <p class="p-5 text-sm text-zinc-500">{{ __('No subjects assigned yet.') }}</p>
                        @endforelse
                    </div>
                </section>
            </div>

            <section class="rounded-lg border border-zinc-200 bg-white p-5 shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
                <h2 class="font-semibold">{{ __('Schedule') }}</h2>
                <p class="mt-2 text-sm text-zinc-500">{{ __('Teacher schedules will appear here after the Scheduling module is enabled.') }}</p>
            </section>
        @else
            <section class="rounded-lg border border-zinc-200 bg-white p-6 text-sm shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
                <p class="font-medium">{{ __('No teacher profile is linked to your account yet.') }}</p>
                <p class="mt-1 text-zinc-500">{{ __('Please contact an administrator to complete your teacher record.') }}</p>
            </section>
        @endif
    </div>
</x-layouts::app>
