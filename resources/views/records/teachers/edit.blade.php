<x-layouts::app :title="__('Edit Teacher')">
    <div class="mx-auto flex w-full max-w-5xl flex-1 flex-col gap-6 p-6">
        <div>
            <flux:heading size="xl">{{ __('Edit Teacher') }}</flux:heading>
            <flux:subheading>{{ $teacher->fullName() }} · {{ $teacher->employee_number }}</flux:subheading>
        </div>

        <form method="POST" action="{{ route('records.teachers.update', $teacher) }}">
            @csrf
            @method('PUT')
            @include('records.teachers.form', ['submit' => __('Save Teacher')])
        </form>
    </div>
</x-layouts::app>
