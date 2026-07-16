<x-layouts::app :title="__('Create Student')">
    <div class="mx-auto flex w-full max-w-6xl flex-1 flex-col gap-6 p-6">
        <div>
            <flux:heading size="xl">{{ __('Create Student') }}</flux:heading>
            <flux:subheading>{{ __('Add a new student profile.') }}</flux:subheading>
        </div>
        <form method="POST" action="{{ route('records.students.store') }}">
            @csrf
            @include('records.students.form', ['submit' => __('Create Student')])
        </form>
    </div>
</x-layouts::app>
