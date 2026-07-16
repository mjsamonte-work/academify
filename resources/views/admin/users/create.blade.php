<x-layouts::app :title="__('Create User')">
    <div class="mx-auto flex w-full max-w-7xl flex-1 flex-col gap-6 p-6">
        <div>
            <flux:heading size="xl">{{ __('Create User') }}</flux:heading>
            <flux:subheading>{{ __('Create a new Academify account and assign access roles.') }}</flux:subheading>
        </div>

        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            @include('admin.users.partials.form', ['submit' => __('Create User')])
        </form>
    </div>
</x-layouts::app>
