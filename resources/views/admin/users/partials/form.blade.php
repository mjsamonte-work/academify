@php
    $selectedRoles = old('roles', isset($user) ? $user->roles->pluck('name')->all() : []);
@endphp

<div class="grid gap-6 lg:grid-cols-[1fr_320px]">
    <section class="rounded-lg border border-zinc-200 bg-white p-5 shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
        <h2 class="text-base font-semibold text-zinc-950 dark:text-white">{{ __('Account Details') }}</h2>

        <div class="mt-5 grid gap-4 md:grid-cols-2">
            <div class="md:col-span-2">
                <label for="name" class="text-sm font-medium">{{ __('Name') }}</label>
                <input id="name" name="name" value="{{ old('name', $user->name ?? '') }}" required class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
                <label for="email" class="text-sm font-medium">{{ __('Email') }}</label>
                <input id="email" name="email" type="email" value="{{ old('email', $user->email ?? '') }}" required class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="phone" class="text-sm font-medium">{{ __('Phone') }}</label>
                <input id="phone" name="phone" value="{{ old('phone', $user->phone ?? '') }}" class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="job_title" class="text-sm font-medium">{{ __('Job Title') }}</label>
                <input id="job_title" name="job_title" value="{{ old('job_title', $user->job_title ?? '') }}" class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                @error('job_title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password" class="text-sm font-medium">{{ __('Password') }}</label>
                <input id="password" name="password" type="password" @if (! isset($user)) required @endif class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password_confirmation" class="text-sm font-medium">{{ __('Confirm Password') }}</label>
                <input id="password_confirmation" name="password_confirmation" type="password" @if (! isset($user)) required @endif class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
            </div>
        </div>
    </section>

    <section class="rounded-lg border border-zinc-200 bg-white p-5 shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
        <h2 class="text-base font-semibold text-zinc-950 dark:text-white">{{ __('Access') }}</h2>

        <div class="mt-5">
            <label for="status" class="text-sm font-medium">{{ __('Status') }}</label>
            <select id="status" name="status" required class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">
                @foreach ($statuses as $status)
                    <option value="{{ $status }}" @selected(old('status', $user->status ?? \App\Models\User::STATUS_ACTIVE) === $status)>
                        {{ str($status)->headline() }}
                    </option>
                @endforeach
            </select>
            @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <fieldset class="mt-5">
            <legend class="text-sm font-medium">{{ __('Roles') }}</legend>
            <div class="mt-3 space-y-3">
                @foreach ($roles as $role)
                    <label class="flex items-center gap-3 text-sm">
                        <input type="checkbox" name="roles[]" value="{{ $role }}" @checked(in_array($role, $selectedRoles, true)) class="rounded border-zinc-300">
                        <span>{{ $role }}</span>
                    </label>
                @endforeach
            </div>
            @error('roles') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            @error('roles.*') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </fieldset>

        <div class="mt-6 flex gap-3">
            <flux:button type="submit" variant="primary">{{ $submit }}</flux:button>
            <flux:button :href="route('admin.users.index')" wire:navigate>{{ __('Cancel') }}</flux:button>
        </div>
    </section>
</div>
