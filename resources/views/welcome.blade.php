<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-zinc-50 text-zinc-950 antialiased dark:bg-zinc-950 dark:text-white">
        <main class="mx-auto flex min-h-screen w-full max-w-6xl flex-col px-6 py-6">
            <header class="flex items-center justify-between border-b border-zinc-200 pb-5 dark:border-zinc-800">
                <a href="{{ route('home') }}" class="flex items-center gap-3" wire:navigate>
                    <span class="flex size-9 items-center justify-center rounded-md bg-zinc-950 text-white dark:bg-white dark:text-zinc-950">
                        <x-app-logo-icon class="size-5 fill-current" />
                    </span>
                    <span class="text-base font-semibold">{{ config('app.name', 'Academify') }}</span>
                </a>

                @auth
                    <flux:button :href="route('dashboard')" wire:navigate variant="primary">{{ __('Dashboard') }}</flux:button>
                @else
                    <flux:button :href="route('login')" wire:navigate variant="primary">{{ __('Log in') }}</flux:button>
                @endauth
            </header>

            <section class="grid flex-1 items-center gap-10 py-16 lg:grid-cols-[1.1fr_0.9fr]">
                <div>
                    <p class="text-sm font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">{{ __('School Management Platform') }}</p>
                    <h1 class="mt-4 max-w-3xl text-4xl font-semibold tracking-normal text-zinc-950 dark:text-white md:text-5xl">
                        {{ __('Academify organizes the first layer of school operations.') }}
                    </h1>
                    <p class="mt-5 max-w-2xl text-base leading-7 text-zinc-600 dark:text-zinc-300">
                        {{ __('A formal workspace for administrators to manage accounts, roles, and access before academic records are introduced.') }}
                    </p>
                </div>

                <div class="rounded-lg border border-zinc-200 bg-white p-6 shadow-xs dark:border-zinc-800 dark:bg-zinc-900">
                    <h2 class="text-lg font-semibold">{{ __('Initial Setup Focus') }}</h2>
                    <div class="mt-5 space-y-4 text-sm text-zinc-600 dark:text-zinc-300">
                        <div class="flex gap-3">
                            <span class="mt-1 size-2 rounded-full bg-zinc-900 dark:bg-white"></span>
                            <p>{{ __('Academify branding and formal dashboard shell.') }}</p>
                        </div>
                        <div class="flex gap-3">
                            <span class="mt-1 size-2 rounded-full bg-zinc-900 dark:bg-white"></span>
                            <p>{{ __('Administrator-led user creation and role assignment.') }}</p>
                        </div>
                        <div class="flex gap-3">
                            <span class="mt-1 size-2 rounded-full bg-zinc-900 dark:bg-white"></span>
                            <p>{{ __('Non-destructive migrations and idempotent seeders.') }}</p>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        @fluxScripts
    </body>
</html>
