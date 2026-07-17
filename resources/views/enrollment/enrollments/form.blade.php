@php($enrollment ??= null)

<section class="rounded-lg border border-zinc-200 bg-white p-5 shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label for="student_id" class="text-sm font-medium">{{ __('Student') }}</label>
            <select id="student_id" name="student_id" required class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                <option value="">{{ __('Select student') }}</option>
                @foreach ($students as $student)
                    <option value="{{ $student->id }}" @selected((int) old('student_id', $enrollment->student_id ?? 0) === $student->id)>{{ $student->fullName() }} · {{ $student->student_number }}</option>
                @endforeach
            </select>
            @error('student_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="school_year_id" class="text-sm font-medium">{{ __('School Year') }}</label>
            <select id="school_year_id" name="school_year_id" required class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                <option value="">{{ __('Select school year') }}</option>
                @foreach ($schoolYears as $schoolYear)
                    <option value="{{ $schoolYear->id }}" @selected((int) old('school_year_id', $enrollment->school_year_id ?? 0) === $schoolYear->id)>{{ $schoolYear->name }}</option>
                @endforeach
            </select>
            @error('school_year_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="grade_level_id" class="text-sm font-medium">{{ __('Grade Level') }}</label>
            <select id="grade_level_id" name="grade_level_id" required class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                <option value="">{{ __('Select grade level') }}</option>
                @foreach ($gradeLevels as $gradeLevel)
                    <option value="{{ $gradeLevel->id }}" @selected((int) old('grade_level_id', $enrollment->grade_level_id ?? 0) === $gradeLevel->id)>{{ $gradeLevel->name }}</option>
                @endforeach
            </select>
            @error('grade_level_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="section_id" class="text-sm font-medium">{{ __('Section') }}</label>
            <select id="section_id" name="section_id" required class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                <option value="">{{ __('Select section') }}</option>
                @foreach ($sections as $section)
                    <option value="{{ $section->id }}" @selected((int) old('section_id', $enrollment->section_id ?? 0) === $section->id)>{{ $section->gradeLevel?->name }} - {{ $section->name }}</option>
                @endforeach
            </select>
            @error('section_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="status" class="text-sm font-medium">{{ __('Status') }}</label>
            <select id="status" name="status" required class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                @foreach ($statuses as $status)
                    <option value="{{ $status }}" @selected(old('status', $enrollment->status ?? \App\Models\Enrollment::STATUS_PENDING) === $status)>{{ str($status)->headline() }}</option>
                @endforeach
            </select>
            @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="enrolled_at" class="text-sm font-medium">{{ __('Enrolled Date') }}</label>
            <input id="enrolled_at" name="enrolled_at" type="date" value="{{ old('enrolled_at', optional($enrollment?->enrolled_at ?? null)->format('Y-m-d')) }}" class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
            @error('enrolled_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="withdrawn_at" class="text-sm font-medium">{{ __('Withdrawn Date') }}</label>
            <input id="withdrawn_at" name="withdrawn_at" type="date" value="{{ old('withdrawn_at', optional($enrollment?->withdrawn_at ?? null)->format('Y-m-d')) }}" class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
            @error('withdrawn_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="completed_at" class="text-sm font-medium">{{ __('Completed Date') }}</label>
            <input id="completed_at" name="completed_at" type="date" value="{{ old('completed_at', optional($enrollment?->completed_at ?? null)->format('Y-m-d')) }}" class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
            @error('completed_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div class="md:col-span-2">
            <label for="notes" class="text-sm font-medium">{{ __('Notes') }}</label>
            <textarea id="notes" name="notes" rows="3" class="mt-1 w-full rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950">{{ old('notes', $enrollment->notes ?? '') }}</textarea>
            @error('notes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="mt-6 flex gap-3">
        <flux:button type="submit" variant="primary">{{ $submit }}</flux:button>
        <flux:button :href="route('enrollment.enrollments.index')" wire:navigate>{{ __('Cancel') }}</flux:button>
    </div>
</section>
