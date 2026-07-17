<x-layouts::app :title="__('Create Grading Period')">
    <div class="mx-auto flex w-full max-w-5xl flex-1 flex-col gap-6 p-6">
        <div><flux:heading size="xl">{{ __('Create Grading Period') }}</flux:heading><flux:subheading>{{ __('Define a school year grading window.') }}</flux:subheading></div>
        <form method="POST" action="{{ route('grades.grading-periods.store') }}">@csrf @include('grades.grading-periods.form', ['submit' => __('Create Period')])</form>
    </div>
</x-layouts::app>
