@php($guardian ??= null)

<section class="rounded-lg border border-zinc-200 bg-white p-5 shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label for="first_name" class="text-sm font-medium">{{ __('First Name') }}</label>
            <input id="first_name" name="first_name" value="{{ old('first_name', $guardian->first_name ?? '') }}" required class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
            @error('first_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="middle_name" class="text-sm font-medium">{{ __('Middle Name') }}</label>
            <input id="middle_name" name="middle_name" value="{{ old('middle_name', $guardian->middle_name ?? '') }}" class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
        </div>
        <div>
            <label for="last_name" class="text-sm font-medium">{{ __('Last Name') }}</label>
            <input id="last_name" name="last_name" value="{{ old('last_name', $guardian->last_name ?? '') }}" required class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
            @error('last_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="status" class="text-sm font-medium">{{ __('Status') }}</label>
            <select id="status" name="status" required class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                @foreach ($statuses as $status)
                    <option value="{{ $status }}" @selected(old('status', $guardian->status ?? \App\Models\Guardian::STATUS_ACTIVE) === $status)>{{ str($status)->headline() }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="email" class="text-sm font-medium">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" value="{{ old('email', $guardian->email ?? '') }}" class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
            @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="phone" class="text-sm font-medium">{{ __('Phone') }}</label>
            <input id="phone" name="phone" value="{{ old('phone', $guardian->phone ?? '') }}" class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
        </div>
        <div>
            <label for="alternate_phone" class="text-sm font-medium">{{ __('Alternate Phone') }}</label>
            <input id="alternate_phone" name="alternate_phone" value="{{ old('alternate_phone', $guardian->alternate_phone ?? '') }}" class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
        </div>
        <div>
            <label for="occupation" class="text-sm font-medium">{{ __('Occupation') }}</label>
            <input id="occupation" name="occupation" value="{{ old('occupation', $guardian->occupation ?? '') }}" class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
        </div>
        <div class="md:col-span-2">
            <label for="address" class="text-sm font-medium">{{ __('Address') }}</label>
            <textarea id="address" name="address" rows="3" class="mt-1 w-full rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950">{{ old('address', $guardian->address ?? '') }}</textarea>
        </div>
    </div>

    <div class="mt-6 flex gap-3">
        <flux:button type="submit" variant="primary">{{ $submit }}</flux:button>
        <flux:button :href="route('records.guardians.index')" wire:navigate>{{ __('Cancel') }}</flux:button>
    </div>
</section>
