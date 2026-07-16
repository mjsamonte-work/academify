<x-layouts::app :title="$student->fullName()">
    <div class="mx-auto flex w-full max-w-7xl flex-1 flex-col gap-6 p-6">
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
            <div>
                <flux:heading size="xl">{{ $student->fullName() }}</flux:heading>
                <flux:subheading>{{ $student->student_number }} · {{ $student->gradeLevel?->name ?? __('No grade level') }}</flux:subheading>
            </div>
            @can('students.update')
                <flux:button :href="route('records.students.edit', $student)" wire:navigate variant="primary">{{ __('Edit Student') }}</flux:button>
            @endcan
        </div>
        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200">{{ session('status') }}</div>
        @endif
        <div class="grid gap-6 lg:grid-cols-[0.8fr_1.2fr]">
            <section class="rounded-lg border border-zinc-200 bg-white p-5 shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
                <h2 class="font-semibold">{{ __('Profile') }}</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div><dt class="text-zinc-500">{{ __('Status') }}</dt><dd><x-records.partials.status-badge :status="$student->status" /></dd></div>
                    <div><dt class="text-zinc-500">{{ __('Section') }}</dt><dd>{{ $student->section?->name ?? __('Not set') }}</dd></div>
                    <div><dt class="text-zinc-500">{{ __('Birthdate') }}</dt><dd>{{ $student->birthdate?->toFormattedDateString() ?? __('Not provided') }}</dd></div>
                    <div><dt class="text-zinc-500">{{ __('Contact') }}</dt><dd>{{ $student->email ?? __('No email') }} · {{ $student->phone ?? __('No phone') }}</dd></div>
                    <div><dt class="text-zinc-500">{{ __('Address') }}</dt><dd>{{ $student->address ?? __('Not provided') }}</dd></div>
                </dl>
            </section>
            <section class="rounded-lg border border-zinc-200 bg-white shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
                <div class="border-b border-zinc-200 px-5 py-4 dark:border-zinc-700"><h2 class="font-semibold">{{ __('Linked Guardians') }}</h2></div>
                <div class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse ($student->guardians as $guardian)
                        <div class="p-5">
                            <div class="flex justify-between gap-4">
                                <div>
                                    <p class="font-medium">{{ $guardian->fullName() }}</p>
                                    <p class="text-sm text-zinc-500">{{ $guardian->pivot->relationship }} @if($guardian->pivot->is_primary_contact) · {{ __('Primary') }} @endif</p>
                                </div>
                                @can('students.update')
                                    <form method="POST" action="{{ route('records.students.guardians.destroy', [$student, $guardian]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <flux:button size="sm" type="submit">{{ __('Detach') }}</flux:button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                    @empty
                        <p class="p-5 text-sm text-zinc-500">{{ __('No guardians linked yet.') }}</p>
                    @endforelse
                </div>
                @can('students.update')
                    <form method="POST" action="{{ route('records.students.guardians.store', $student) }}" class="grid gap-3 border-t border-zinc-200 p-5 dark:border-zinc-700 md:grid-cols-2">
                        @csrf
                        <select name="guardian_id" required class="h-10 rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                            <option value="">{{ __('Select guardian') }}</option>
                            @foreach ($guardians as $guardian)
                                <option value="{{ $guardian->id }}">{{ $guardian->fullName() }}</option>
                            @endforeach
                        </select>
                        <input name="relationship" placeholder="{{ __('Relationship') }}" required class="h-10 rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_primary_contact" value="1"> {{ __('Primary contact') }}</label>
                        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="can_pick_up" value="1"> {{ __('Can pick up') }}</label>
                        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="receives_notifications" value="1" checked> {{ __('Receives notifications') }}</label>
                        <div><flux:button type="submit" variant="primary">{{ __('Attach Guardian') }}</flux:button></div>
                    </form>
                @endcan
            </section>
        </div>
    </div>
</x-layouts::app>
