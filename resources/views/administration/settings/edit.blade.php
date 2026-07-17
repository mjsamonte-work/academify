<x-layouts::app :title="__('System Settings')">
    <div class="mx-auto flex w-full max-w-5xl flex-1 flex-col gap-6 p-6">
        <div>
            <flux:heading size="xl">{{ __('System Settings') }}</flux:heading>
            <flux:subheading>{{ __('Manage school identity, academic defaults, and report labels.') }}</flux:subheading>
        </div>

        <form method="POST" action="{{ route('administration.settings.update') }}" class="flex flex-col gap-6">
            @csrf
            @method('PUT')

            <section class="rounded-lg border border-zinc-200 bg-white shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
                <div class="border-b border-zinc-200 p-4 dark:border-zinc-700">
                    <p class="font-medium">{{ __('School Identity') }}</p>
                </div>
                <div class="grid gap-4 p-4 md:grid-cols-2">
                    <label class="grid gap-1 text-sm">
                        <span>{{ __('School Name') }}</span>
                        <input name="school_name" value="{{ old('school_name', $settings['school_name']->displayValue()) }}" class="h-10 rounded-md border border-zinc-300 bg-white px-3 dark:border-zinc-700 dark:bg-zinc-950">
                        @error('school_name')<span class="text-xs text-red-600">{{ $message }}</span>@enderror
                    </label>
                    <label class="grid gap-1 text-sm">
                        <span>{{ __('School Code') }}</span>
                        <input name="school_code" value="{{ old('school_code', $settings['school_code']->displayValue()) }}" class="h-10 rounded-md border border-zinc-300 bg-white px-3 dark:border-zinc-700 dark:bg-zinc-950">
                        @error('school_code')<span class="text-xs text-red-600">{{ $message }}</span>@enderror
                    </label>
                    <label class="grid gap-1 text-sm">
                        <span>{{ __('School Email') }}</span>
                        <input name="school_email" value="{{ old('school_email', $settings['school_email']->displayValue()) }}" class="h-10 rounded-md border border-zinc-300 bg-white px-3 dark:border-zinc-700 dark:bg-zinc-950">
                        @error('school_email')<span class="text-xs text-red-600">{{ $message }}</span>@enderror
                    </label>
                    <label class="grid gap-1 text-sm">
                        <span>{{ __('School Phone') }}</span>
                        <input name="school_phone" value="{{ old('school_phone', $settings['school_phone']->displayValue()) }}" class="h-10 rounded-md border border-zinc-300 bg-white px-3 dark:border-zinc-700 dark:bg-zinc-950">
                        @error('school_phone')<span class="text-xs text-red-600">{{ $message }}</span>@enderror
                    </label>
                    <label class="grid gap-1 text-sm md:col-span-2">
                        <span>{{ __('School Address') }}</span>
                        <textarea name="school_address" rows="3" class="rounded-md border border-zinc-300 bg-white px-3 py-2 dark:border-zinc-700 dark:bg-zinc-950">{{ old('school_address', $settings['school_address']->displayValue()) }}</textarea>
                        @error('school_address')<span class="text-xs text-red-600">{{ $message }}</span>@enderror
                    </label>
                </div>
            </section>

            <section class="rounded-lg border border-zinc-200 bg-white shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
                <div class="border-b border-zinc-200 p-4 dark:border-zinc-700">
                    <p class="font-medium">{{ __('Academic Defaults') }}</p>
                </div>
                <div class="grid gap-4 p-4 md:grid-cols-2">
                    <label class="grid gap-1 text-sm">
                        <span>{{ __('Active School Year') }}</span>
                        <select name="active_school_year_id" class="h-10 rounded-md border border-zinc-300 bg-white px-3 dark:border-zinc-700 dark:bg-zinc-950">
                            <option value="">{{ __('Not selected') }}</option>
                            @foreach($schoolYears as $schoolYear)
                                <option value="{{ $schoolYear->id }}" @selected((int) old('active_school_year_id', $settings['active_school_year_id']->displayValue()) === $schoolYear->id)>{{ $schoolYear->name }}</option>
                            @endforeach
                        </select>
                        @error('active_school_year_id')<span class="text-xs text-red-600">{{ $message }}</span>@enderror
                    </label>
                    <label class="grid gap-1 text-sm">
                        <span>{{ __('Default Grading Period') }}</span>
                        <select name="default_grading_period_id" class="h-10 rounded-md border border-zinc-300 bg-white px-3 dark:border-zinc-700 dark:bg-zinc-950">
                            <option value="">{{ __('Not selected') }}</option>
                            @foreach($gradingPeriods as $period)
                                <option value="{{ $period->id }}" @selected((int) old('default_grading_period_id', $settings['default_grading_period_id']->displayValue()) === $period->id)>{{ $period->name }}</option>
                            @endforeach
                        </select>
                        @error('default_grading_period_id')<span class="text-xs text-red-600">{{ $message }}</span>@enderror
                    </label>
                    <label class="grid gap-1 text-sm md:col-span-2">
                        <span>{{ __('Timezone Label') }}</span>
                        <input name="timezone_label" value="{{ old('timezone_label', $settings['timezone_label']->displayValue()) }}" class="h-10 rounded-md border border-zinc-300 bg-white px-3 dark:border-zinc-700 dark:bg-zinc-950">
                        @error('timezone_label')<span class="text-xs text-red-600">{{ $message }}</span>@enderror
                    </label>
                </div>
            </section>

            <section class="rounded-lg border border-zinc-200 bg-white shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
                <div class="border-b border-zinc-200 p-4 dark:border-zinc-700">
                    <p class="font-medium">{{ __('Report Defaults') }}</p>
                </div>
                <div class="grid gap-4 p-4">
                    <label class="grid gap-1 text-sm">
                        <span>{{ __('Report Footer Text') }}</span>
                        <textarea name="report_footer_text" rows="3" class="rounded-md border border-zinc-300 bg-white px-3 py-2 dark:border-zinc-700 dark:bg-zinc-950">{{ old('report_footer_text', $settings['report_footer_text']->displayValue()) }}</textarea>
                        @error('report_footer_text')<span class="text-xs text-red-600">{{ $message }}</span>@enderror
                    </label>
                    <label class="grid gap-1 text-sm">
                        <span>{{ __('Prepared By Label') }}</span>
                        <input name="report_prepared_by_label" value="{{ old('report_prepared_by_label', $settings['report_prepared_by_label']->displayValue()) }}" class="h-10 rounded-md border border-zinc-300 bg-white px-3 dark:border-zinc-700 dark:bg-zinc-950">
                        @error('report_prepared_by_label')<span class="text-xs text-red-600">{{ $message }}</span>@enderror
                    </label>
                </div>
            </section>

            <div class="flex justify-end">
                <flux:button type="submit" variant="primary">{{ __('Save Settings') }}</flux:button>
            </div>
        </form>
    </div>
</x-layouts::app>
