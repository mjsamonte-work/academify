<x-layouts::auth :title="__('Log in')">
    <div class="flex flex-col gap-6" x-data="{ email: @js(old('email', '')), password: '' }">
        <x-auth-header :title="__('Welcome to Academify')" :description="__('Sign in with your school account to continue.')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <x-passkey-verify />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('Email address')"
                x-model="email"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="email@example.com"
            />

            <!-- Password -->
            <div class="relative">
                <flux:input
                    name="password"
                    :label="__('Password')"
                    x-model="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    :placeholder="__('Password')"
                    viewable
                />

                @if (Route::has('password.request'))
                    <flux:link class="absolute top-0 text-sm end-0" :href="route('password.request')" wire:navigate>
                        {{ __('Forgot your password?') }}
                    </flux:link>
                @endif
            </div>

            <!-- Remember Me -->
            <flux:checkbox name="remember" :label="__('Remember me')" :checked="old('remember')" />

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full" data-test="login-button">
                    {{ __('Log in') }}
                </flux:button>
            </div>
        </form>

        @env('local')
            <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-900/60">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ __('Local Development Accounts') }}</p>
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ __('Use password: password') }}</p>
                    </div>
                    <span class="rounded-full bg-zinc-200 px-2 py-1 text-xs font-medium text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">{{ __('Local') }}</span>
                </div>

                <div class="mt-4 grid gap-2">
                    @foreach ([
                        'Administrator' => 'admin@academify.local',
                        'Teacher' => 'teacher@academify.local',
                        'Student' => 'student@academify.local',
                        'Parent/Guardian' => 'guardian@academify.local',
                    ] as $role => $email)
                        <button
                            type="button"
                            class="flex items-center justify-between rounded-md border border-zinc-200 bg-white px-3 py-2 text-left text-sm transition hover:border-zinc-300 hover:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-950 dark:hover:border-zinc-600 dark:hover:bg-zinc-900"
                            x-on:click="email = @js($email); password = 'password'"
                        >
                            <span class="font-medium text-zinc-800 dark:text-zinc-200">{{ $role }}</span>
                            <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $email }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
        @endenv

        <p class="text-center text-sm text-zinc-600 dark:text-zinc-400">
            {{ __('Accounts are created by an Academify administrator.') }}
        </p>
    </div>
</x-layouts::auth>
