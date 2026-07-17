@php
    $teacher ??= null;
    $selectedSubjectIds = collect(old('subject_ids', $teacher ? $teacher->subjects->pluck('id')->all() : []))
        ->map(fn ($id) => (int) $id)
        ->all();
@endphp

<section class="rounded-lg border border-zinc-200 bg-white p-5 shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label for="employee_number" class="text-sm font-medium">{{ __('Employee Number') }}</label>
            <input id="employee_number" name="employee_number" value="{{ old('employee_number', $teacher->employee_number ?? '') }}" required class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
            @error('employee_number') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="status" class="text-sm font-medium">{{ __('Status') }}</label>
            <select id="status" name="status" required class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                @foreach ($statuses as $status)
                    <option value="{{ $status }}" @selected(old('status', $teacher->status ?? \App\Models\Teacher::STATUS_ACTIVE) === $status)>{{ str($status)->headline() }}</option>
                @endforeach
            </select>
            @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="first_name" class="text-sm font-medium">{{ __('First Name') }}</label>
            <input id="first_name" name="first_name" value="{{ old('first_name', $teacher->first_name ?? '') }}" required class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
            @error('first_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="middle_name" class="text-sm font-medium">{{ __('Middle Name') }}</label>
            <input id="middle_name" name="middle_name" value="{{ old('middle_name', $teacher->middle_name ?? '') }}" class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
        </div>
        <div>
            <label for="last_name" class="text-sm font-medium">{{ __('Last Name') }}</label>
            <input id="last_name" name="last_name" value="{{ old('last_name', $teacher->last_name ?? '') }}" required class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
            @error('last_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="preferred_name" class="text-sm font-medium">{{ __('Preferred Name') }}</label>
            <input id="preferred_name" name="preferred_name" value="{{ old('preferred_name', $teacher->preferred_name ?? '') }}" class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
        </div>

        <div>
            <label for="email" class="text-sm font-medium">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" value="{{ old('email', $teacher->email ?? '') }}" class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
            @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="phone" class="text-sm font-medium">{{ __('Phone') }}</label>
            <input id="phone" name="phone" value="{{ old('phone', $teacher->phone ?? '') }}" class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
        </div>
        <div>
            <label for="job_title" class="text-sm font-medium">{{ __('Job Title') }}</label>
            <input id="job_title" name="job_title" value="{{ old('job_title', $teacher->job_title ?? '') }}" class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
        </div>
        <div>
            <label for="department" class="text-sm font-medium">{{ __('Department') }}</label>
            <input id="department" name="department" value="{{ old('department', $teacher->department ?? '') }}" class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
        </div>
        <div>
            <label for="employment_type" class="text-sm font-medium">{{ __('Employment Type') }}</label>
            <select id="employment_type" name="employment_type" class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                <option value="">{{ __('Select employment type') }}</option>
                @foreach ($employmentTypes as $employmentType)
                    <option value="{{ $employmentType }}" @selected(old('employment_type', $teacher->employment_type ?? '') === $employmentType)>{{ str($employmentType)->replace('_', ' ')->headline() }}</option>
                @endforeach
            </select>
            @error('employment_type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="hired_at" class="text-sm font-medium">{{ __('Hired Date') }}</label>
            <input id="hired_at" name="hired_at" type="date" value="{{ old('hired_at', optional($teacher?->hired_at ?? null)->format('Y-m-d')) }}" class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
            @error('hired_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="user_id" class="text-sm font-medium">{{ __('Linked User Account') }}</label>
            <select id="user_id" name="user_id" class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                <option value="">{{ __('No linked account') }}</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" @selected((int) old('user_id', $teacher->user_id ?? 0) === $user->id)>{{ $user->name }} · {{ $user->email }}</option>
                @endforeach
            </select>
            @error('user_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div class="md:col-span-2">
            <label for="subject_ids" class="text-sm font-medium">{{ __('Assigned Subjects') }}</label>
            <select id="subject_ids" name="subject_ids[]" multiple class="mt-1 min-h-32 w-full rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                @foreach ($subjects as $subject)
                    <option value="{{ $subject->id }}" @selected(in_array($subject->id, $selectedSubjectIds, true))>{{ $subject->code }} · {{ $subject->name }}</option>
                @endforeach
            </select>
            @error('subject_ids') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            @error('subject_ids.*') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div class="md:col-span-2">
            <label for="address" class="text-sm font-medium">{{ __('Address') }}</label>
            <textarea id="address" name="address" rows="3" class="mt-1 w-full rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950">{{ old('address', $teacher->address ?? '') }}</textarea>
        </div>
    </div>

    <div class="mt-6 flex gap-3">
        <flux:button type="submit" variant="primary">{{ $submit }}</flux:button>
        <flux:button :href="route('records.teachers.index')" wire:navigate>{{ __('Cancel') }}</flux:button>
    </div>
</section>
