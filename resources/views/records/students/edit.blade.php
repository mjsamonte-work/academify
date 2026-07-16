<x-layouts::app :title="__('Edit Student')">
    <div class="mx-auto flex w-full max-w-6xl flex-1 flex-col gap-6 p-6">
        <div>
            <flux:heading size="xl">{{ __('Edit Student') }}</flux:heading>
            <flux:subheading>{{ __('Update student profile and academic placement.') }}</flux:subheading>
        </div>
        <form method="POST" action="{{ route('records.students.update', $student) }}">
            @csrf
            @method('PUT')
            @include('records.students.form', ['submit' => __('Save Changes')])
        </form>
    </div>
</x-layouts::app>
