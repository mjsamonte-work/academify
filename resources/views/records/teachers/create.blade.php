<x-layouts::app :title="__('Create Teacher')">
    <div class="mx-auto flex w-full max-w-5xl flex-1 flex-col gap-6 p-6">
        <div>
            <flux:heading size="xl">{{ __('Create Teacher') }}</flux:heading>
            <flux:subheading>{{ __('Add a teacher profile, account link, and subject assignments.') }}</flux:subheading>
        </div>

        <form method="POST" action="{{ route('records.teachers.store') }}">
            @csrf
            @include('records.teachers.form', ['submit' => __('Create Teacher')])
        </form>
    </div>
</x-layouts::app>
