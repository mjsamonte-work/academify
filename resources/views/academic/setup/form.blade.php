@php
    $record ??= null;
@endphp

<section class="rounded-lg border border-zinc-200 bg-white p-5 shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
    <div class="grid gap-5 md:grid-cols-2">
        @if ($resource === 'terms')
            <div class="md:col-span-2">
                <label for="school_year_id" class="text-sm font-medium">{{ __('School Year') }}</label>
                <select id="school_year_id" name="school_year_id" required class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                    <option value="">{{ __('Select school year') }}</option>
                    @foreach ($options['schoolYears'] as $schoolYear)
                        <option value="{{ $schoolYear->id }}" @selected((int) old('school_year_id', $record->school_year_id ?? 0) === $schoolYear->id)>
                            {{ $schoolYear->name }}
                        </option>
                    @endforeach
                </select>
                @error('school_year_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        @endif

        @if ($resource === 'sections')
            <div class="md:col-span-2">
                <label for="grade_level_id" class="text-sm font-medium">{{ __('Grade Level') }}</label>
                <select id="grade_level_id" name="grade_level_id" required class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                    <option value="">{{ __('Select grade level') }}</option>
                    @foreach ($options['gradeLevels'] as $gradeLevel)
                        <option value="{{ $gradeLevel->id }}" @selected((int) old('grade_level_id', $record->grade_level_id ?? 0) === $gradeLevel->id)>
                            {{ $gradeLevel->name }}
                        </option>
                    @endforeach
                </select>
                @error('grade_level_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        @endif

        <div class="md:col-span-2">
            <label for="name" class="text-sm font-medium">{{ __('Name') }}</label>
            <input id="name" name="name" value="{{ old('name', $record->name ?? '') }}" required class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        @if (in_array($resource, ['grade-levels', 'sections', 'subjects', 'classrooms'], true))
            <div>
                <label for="code" class="text-sm font-medium">{{ __('Code') }}</label>
                <input id="code" name="code" value="{{ old('code', $record->code ?? '') }}" required class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm uppercase dark:border-zinc-700 dark:bg-zinc-950">
                @error('code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        @endif

        @if (in_array($resource, ['school-years', 'terms'], true))
            <div>
                <label for="starts_at" class="text-sm font-medium">{{ __('Start Date') }}</label>
                <input id="starts_at" name="starts_at" type="date" value="{{ old('starts_at', optional($record?->starts_at ?? null)->format('Y-m-d')) }}" required class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                @error('starts_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="ends_at" class="text-sm font-medium">{{ __('End Date') }}</label>
                <input id="ends_at" name="ends_at" type="date" value="{{ old('ends_at', optional($record?->ends_at ?? null)->format('Y-m-d')) }}" required class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                @error('ends_at') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        @endif

        @if (in_array($resource, ['terms', 'grade-levels'], true))
            <div>
                <label for="sort_order" class="text-sm font-medium">{{ __('Sort Order') }}</label>
                <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $record->sort_order ?? 0) }}" required class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                @error('sort_order') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        @endif

        @if (in_array($resource, ['sections', 'classrooms'], true))
            <div>
                <label for="capacity" class="text-sm font-medium">{{ __('Capacity') }}</label>
                <input id="capacity" name="capacity" type="number" min="1" value="{{ old('capacity', $record->capacity ?? '') }}" class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                @error('capacity') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        @endif

        @if ($resource === 'classrooms')
            <div>
                <label for="location" class="text-sm font-medium">{{ __('Location') }}</label>
                <input id="location" name="location" value="{{ old('location', $record->location ?? '') }}" class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                @error('location') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        @endif

        @if ($resource === 'subjects')
            <div class="md:col-span-2">
                <label for="description" class="text-sm font-medium">{{ __('Description') }}</label>
                <textarea id="description" name="description" rows="4" class="mt-1 w-full rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950">{{ old('description', $record->description ?? '') }}</textarea>
                @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        @endif

        <div>
            <label for="status" class="text-sm font-medium">{{ __('Status') }}</label>
            <select id="status" name="status" required class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                @foreach ($statuses as $status)
                    <option value="{{ $status }}" @selected(old('status', $record->status ?? \App\Models\SchoolYear::STATUS_ACTIVE) === $status)>
                        {{ str($status)->headline() }}
                    </option>
                @endforeach
            </select>
            @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        @if ($resource === 'school-years')
            <div class="flex items-end">
                <label class="flex items-center gap-3 text-sm">
                    <input type="checkbox" name="is_active" value="1" @checked((bool) old('is_active', $record->is_active ?? false)) class="rounded border-zinc-300">
                    <span>{{ __('Set as active school year') }}</span>
                </label>
                @error('is_active') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        @endif
    </div>

    <div class="mt-6 flex gap-3">
        <flux:button type="submit" variant="primary">{{ $submit }}</flux:button>
        <flux:button :href="route($config['route'].'.index')" wire:navigate>{{ __('Cancel') }}</flux:button>
    </div>
</section>
