<x-layouts::app :title="__('Create Assessment')">
    <div class="mx-auto flex w-full max-w-5xl flex-1 flex-col gap-6 p-6">
        <div><flux:heading size="xl">{{ __('Create Assessment') }}</flux:heading><flux:subheading>{{ __('Create a grade entry target for a scheduled class.') }}</flux:subheading></div>
        <form method="POST" action="{{ route('grades.assessments.store') }}">@csrf @include('grades.assessments.form', ['submit' => __('Create Assessment')])</form>
    </div>
</x-layouts::app>
