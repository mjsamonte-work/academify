<x-layouts::app :title="__('Create').' '.$config['singular']">
    <div class="mx-auto flex w-full max-w-5xl flex-1 flex-col gap-6 p-6">
        <div>
            <flux:heading size="xl">{{ __('Create') }} {{ $config['singular'] }}</flux:heading>
            <flux:subheading>{{ __('Add a new academic setup record.') }}</flux:subheading>
        </div>

        <form method="POST" action="{{ route($config['route'].'.store') }}">
            @csrf
            @include('academic.setup.form', ['record' => null, 'submit' => __('Create')])
        </form>
    </div>
</x-layouts::app>
