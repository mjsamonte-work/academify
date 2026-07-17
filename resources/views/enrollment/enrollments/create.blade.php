<x-layouts::app :title="__('Create Enrollment')">
    <div class="mx-auto flex w-full max-w-5xl flex-1 flex-col gap-6 p-6">
        <div>
            <flux:heading size="xl">{{ __('Create Enrollment') }}</flux:heading>
            <flux:subheading>{{ __('Place a student into a school year, grade level, and section.') }}</flux:subheading>
        </div>

        <form method="POST" action="{{ route('enrollment.enrollments.store') }}">
            @csrf
            @include('enrollment.enrollments.form', ['submit' => __('Create Enrollment')])
        </form>
    </div>
</x-layouts::app>
