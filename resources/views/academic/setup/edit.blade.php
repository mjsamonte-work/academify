<x-layouts::app :title="__('Edit').' '.$config['singular']">
    <div class="mx-auto flex w-full max-w-5xl flex-1 flex-col gap-6 p-6">
        <div>
            <flux:heading size="xl">{{ __('Edit') }} {{ $config['singular'] }}</flux:heading>
            <flux:subheading>{{ __('Update academic setup details and status.') }}</flux:subheading>
        </div>

        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route($config['route'].'.update', $record) }}">
            @csrf
            @method('PUT')
            @include('academic.setup.form', ['submit' => __('Save Changes')])
        </form>
    </div>
</x-layouts::app>
