@php($classSchedule ??= null)

<section class="rounded-lg border border-zinc-200 bg-white p-5 shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label for="school_year_id" class="text-sm font-medium">{{ __('School Year') }}</label>
            <select id="school_year_id" name="school_year_id" required class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                <option value="">{{ __('Select school year') }}</option>
                @foreach ($schoolYears as $schoolYear)
                    <option value="{{ $schoolYear->id }}" @selected((int) old('school_year_id', $classSchedule->school_year_id ?? 0) === $schoolYear->id)>{{ $schoolYear->name }}</option>
                @endforeach
            </select>
            @error('school_year_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="status" class="text-sm font-medium">{{ __('Status') }}</label>
            <select id="status" name="status" required class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                @foreach ($statuses as $status)
                    <option value="{{ $status }}" @selected(old('status', $classSchedule->status ?? \App\Models\ClassSchedule::STATUS_ACTIVE) === $status)>{{ str($status)->headline() }}</option>
                @endforeach
            </select>
            @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="section_id" class="text-sm font-medium">{{ __('Section') }}</label>
            <select id="section_id" name="section_id" required class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                <option value="">{{ __('Select section') }}</option>
                @foreach ($sections as $section)
                    <option value="{{ $section->id }}" @selected((int) old('section_id', $classSchedule->section_id ?? 0) === $section->id)>{{ $section->gradeLevel?->name }} - {{ $section->name }}</option>
                @endforeach
            </select>
            @error('section_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="subject_id" class="text-sm font-medium">{{ __('Subject') }}</label>
            <select id="subject_id" name="subject_id" required class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                <option value="">{{ __('Select subject') }}</option>
                @foreach ($subjects as $subject)
                    <option value="{{ $subject->id }}" @selected((int) old('subject_id', $classSchedule->subject_id ?? 0) === $subject->id)>{{ $subject->code }} · {{ $subject->name }}</option>
                @endforeach
            </select>
            @error('subject_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="teacher_id" class="text-sm font-medium">{{ __('Teacher') }}</label>
            <select id="teacher_id" name="teacher_id" required class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                <option value="">{{ __('Select teacher') }}</option>
                @foreach ($teachers as $teacher)
                    <option value="{{ $teacher->id }}" @selected((int) old('teacher_id', $classSchedule->teacher_id ?? 0) === $teacher->id)>{{ $teacher->fullName() }}</option>
                @endforeach
            </select>
            @error('teacher_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="classroom_id" class="text-sm font-medium">{{ __('Classroom') }}</label>
            <select id="classroom_id" name="classroom_id" class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                <option value="">{{ __('No classroom') }}</option>
                @foreach ($classrooms as $classroom)
                    <option value="{{ $classroom->id }}" @selected((int) old('classroom_id', $classSchedule->classroom_id ?? 0) === $classroom->id)>{{ $classroom->code }} · {{ $classroom->name }}</option>
                @endforeach
            </select>
            @error('classroom_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="day_of_week" class="text-sm font-medium">{{ __('Day') }}</label>
            <select id="day_of_week" name="day_of_week" required class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                <option value="">{{ __('Select day') }}</option>
                @foreach ($days as $day)
                    <option value="{{ $day }}" @selected(old('day_of_week', $classSchedule->day_of_week ?? '') === $day)>{{ str($day)->headline() }}</option>
                @endforeach
            </select>
            @error('day_of_week') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label for="starts_at" class="text-sm font-medium">{{ __('Start Time') }}</label>
                <input id="starts_at" name="starts_at" type="time" value="{{ old('starts_at', isset($classSchedule->starts_at) ? substr($classSchedule->starts_at, 0, 5) : '') }}" required class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                @error('starts_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="ends_at" class="text-sm font-medium">{{ __('End Time') }}</label>
                <input id="ends_at" name="ends_at" type="time" value="{{ old('ends_at', isset($classSchedule->ends_at) ? substr($classSchedule->ends_at, 0, 5) : '') }}" required class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                @error('ends_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>
        <div class="md:col-span-2">
            <label for="notes" class="text-sm font-medium">{{ __('Notes') }}</label>
            <textarea id="notes" name="notes" rows="3" class="mt-1 w-full rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950">{{ old('notes', $classSchedule->notes ?? '') }}</textarea>
            @error('notes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="mt-6 flex gap-3">
        <flux:button type="submit" variant="primary">{{ $submit }}</flux:button>
        <flux:button :href="route('scheduling.class-schedules.index')" wire:navigate>{{ __('Cancel') }}</flux:button>
    </div>
</section>
