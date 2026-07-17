<x-layouts::app :title="__('Edit Schedule')">
    <div class="mx-auto flex w-full max-w-5xl flex-1 flex-col gap-6 p-6">
        <div>
            <flux:heading size="xl">{{ __('Edit Schedule') }}</flux:heading>
            <flux:subheading>{{ $classSchedule->subject?->name ?? __('Class schedule') }}</flux:subheading>
        </div>

        <form method="POST" action="{{ route('scheduling.class-schedules.update', $classSchedule) }}">
            @csrf
            @method('PUT')
            @include('scheduling.class-schedules.form', ['submit' => __('Save Schedule')])
        </form>
    </div>
</x-layouts::app>
