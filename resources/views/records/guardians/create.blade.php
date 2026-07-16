<x-layouts::app :title="__('Create Guardian')">
    <div class="mx-auto flex w-full max-w-6xl flex-1 flex-col gap-6 p-6">
        <div>
            <flux:heading size="xl">{{ __('Create Guardian') }}</flux:heading>
            <flux:subheading>{{ __('Add a new guardian profile.') }}</flux:subheading>
        </div>
        <form method="POST" action="{{ route('records.guardians.store') }}">
            @csrf
            @include('records.guardians.form', ['submit' => __('Create Guardian')])
        </form>
    </div>
</x-layouts::app>
