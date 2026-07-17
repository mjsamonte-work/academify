<x-layouts::app :title="__('Edit Enrollment')">
    <div class="mx-auto flex w-full max-w-5xl flex-1 flex-col gap-6 p-6">
        <div>
            <flux:heading size="xl">{{ __('Edit Enrollment') }}</flux:heading>
            <flux:subheading>{{ $enrollment->student?->fullName() ?? __('Enrollment record') }}</flux:subheading>
        </div>

        <form method="POST" action="{{ route('enrollment.enrollments.update', $enrollment) }}">
            @csrf
            @method('PUT')
            @include('enrollment.enrollments.form', ['submit' => __('Save Enrollment')])
        </form>
    </div>
</x-layouts::app>
