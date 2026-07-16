<x-layouts::app :title="__('Edit User')">
    <div class="mx-auto flex w-full max-w-7xl flex-1 flex-col gap-6 p-6">
        <div>
            <flux:heading size="xl">{{ __('Edit User') }}</flux:heading>
            <flux:subheading>{{ __('Update account details, status, and role assignment.') }}</flux:subheading>
        </div>

        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')
            @include('admin.users.partials.form', ['submit' => __('Save Changes')])
        </form>
    </div>
</x-layouts::app>
