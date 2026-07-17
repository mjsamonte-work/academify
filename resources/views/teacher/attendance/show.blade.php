<x-layouts::app :title="__('Attendance Entry')">
    <div class="mx-auto flex w-full max-w-7xl flex-1 flex-col gap-6 p-6">
        <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
            <div>
                <flux:heading size="xl">{{ __('Attendance Entry') }}</flux:heading>
                <flux:subheading>{{ $classSchedule->subject->name }} · {{ $classSchedule->section->code }} · {{ $attendanceDate }}</flux:subheading>
            </div>
            <form method="GET" action="{{ route('teacher.attendance.show', $classSchedule) }}" class="flex gap-2">
                <input name="attendance_date" type="date" value="{{ $attendanceDate }}" class="h-10 rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                <flux:button type="submit">{{ __('Open Date') }}</flux:button>
            </form>
        </div>

        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200">{{ session('status') }}</div>
        @endif

        <section class="rounded-lg border border-zinc-200 bg-white p-5 shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
            <div class="grid gap-4 text-sm md:grid-cols-4">
                <div><p class="text-zinc-500">{{ __('Schedule') }}</p><p class="mt-1 font-medium">{{ str($classSchedule->day_of_week)->headline() }} · {{ substr($classSchedule->starts_at, 0, 5) }} - {{ substr($classSchedule->ends_at, 0, 5) }}</p></div>
                <div><p class="text-zinc-500">{{ __('School Year') }}</p><p class="mt-1 font-medium">{{ $classSchedule->schoolYear->name }}</p></div>
                <div><p class="text-zinc-500">{{ __('Room') }}</p><p class="mt-1 font-medium">{{ $classSchedule->classroom?->code ?? __('No room') }}</p></div>
                <div><p class="text-zinc-500">{{ __('Session Status') }}</p><p class="mt-1">@if($session)<x-records.partials.status-badge :status="$session->status" />@else {{ __('Not started') }} @endif</p></div>
            </div>
        </section>

        <form method="POST" action="{{ route('teacher.attendance.save', $classSchedule) }}" class="rounded-lg border border-zinc-200 bg-white shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
            @csrf
            <input type="hidden" name="attendance_date" value="{{ $attendanceDate }}">
            <div class="grid gap-4 border-b border-zinc-200 p-5 dark:border-zinc-700 md:grid-cols-[180px_1fr]">
                <div>
                    <label for="session_status" class="text-sm font-medium">{{ __('Save As') }}</label>
                    <select id="session_status" name="session_status" class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                        <option value="draft" @selected(old('session_status', $session->status ?? 'draft') === 'draft')>{{ __('Draft') }}</option>
                        <option value="submitted" @selected(old('session_status', $session->status ?? '') === 'submitted')>{{ __('Submitted') }}</option>
                    </select>
                </div>
                <div>
                    <label for="notes" class="text-sm font-medium">{{ __('Session Notes') }}</label>
                    <input id="notes" name="notes" value="{{ old('notes', $session->notes ?? '') }}" class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-zinc-200 text-xs uppercase text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
                        <tr>
                            <th class="px-5 py-3">{{ __('Student') }}</th>
                            <th class="px-5 py-3">{{ __('Status') }}</th>
                            <th class="px-5 py-3">{{ __('Notes') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse ($students as $index => $student)
                            @php($record = $recordsByStudent->get($student->id))
                            <tr>
                                <td class="px-5 py-4">
                                    <input type="hidden" name="records[{{ $index }}][student_id]" value="{{ $student->id }}">
                                    <p class="font-medium text-zinc-950 dark:text-white">{{ $student->fullName() }}</p>
                                    <p class="text-zinc-500">{{ $student->student_number }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <select name="records[{{ $index }}][status]" class="h-10 rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                                        @foreach (\App\Models\AttendanceRecord::statuses() as $status)
                                            <option value="{{ $status }}" @selected(old("records.$index.status", $record->status ?? \App\Models\AttendanceRecord::STATUS_PRESENT) === $status)>{{ str($status)->headline() }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-5 py-4">
                                    <input name="records[{{ $index }}][notes]" value="{{ old("records.$index.notes", $record->notes ?? '') }}" class="h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-5 py-10 text-center text-zinc-500">{{ __('No enrolled students found for this scheduled section.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-zinc-200 p-5 dark:border-zinc-700">
                <flux:button type="submit" variant="primary">{{ __('Save Attendance') }}</flux:button>
            </div>
        </form>
    </div>
</x-layouts::app>
