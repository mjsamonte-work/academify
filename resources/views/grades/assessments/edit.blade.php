<x-layouts::app :title="__('Edit Assessment')">
    <div class="mx-auto flex w-full max-w-5xl flex-1 flex-col gap-6 p-6">
        <div><flux:heading size="xl">{{ __('Edit Assessment') }}</flux:heading><flux:subheading>{{ $assessment->title }}</flux:subheading></div>
        <form method="POST" action="{{ route('grades.assessments.update', $assessment) }}">@csrf @method('PUT') @include('grades.assessments.form', ['submit' => __('Save Assessment')])</form>
    </div>
</x-layouts::app>
