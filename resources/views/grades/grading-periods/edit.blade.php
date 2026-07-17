<x-layouts::app :title="__('Edit Grading Period')">
    <div class="mx-auto flex w-full max-w-5xl flex-1 flex-col gap-6 p-6">
        <div><flux:heading size="xl">{{ __('Edit Grading Period') }}</flux:heading><flux:subheading>{{ $gradingPeriod->name }}</flux:subheading></div>
        <form method="POST" action="{{ route('grades.grading-periods.update', $gradingPeriod) }}">@csrf @method('PUT') @include('grades.grading-periods.form', ['submit' => __('Save Period')])</form>
    </div>
</x-layouts::app>
