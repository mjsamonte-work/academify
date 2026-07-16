<x-layouts::app :title="__('Edit Guardian')">
    <div class="mx-auto flex w-full max-w-6xl flex-1 flex-col gap-6 p-6">
        <div>
            <flux:heading size="xl">{{ __('Edit Guardian') }}</flux:heading>
            <flux:subheading>{{ __('Update guardian profile and status.') }}</flux:subheading>
        </div>
        <form method="POST" action="{{ route('records.guardians.update', $guardian) }}">
            @csrf
            @method('PUT')
            @include('records.guardians.form', ['submit' => __('Save Changes')])
        </form>
    </div>
</x-layouts::app>
