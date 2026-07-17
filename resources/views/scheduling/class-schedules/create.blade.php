<x-layouts::app :title="__('Create Schedule')">
    <div class="mx-auto flex w-full max-w-5xl flex-1 flex-col gap-6 p-6">
        <div>
            <flux:heading size="xl">{{ __('Create Schedule') }}</flux:heading>
            <flux:subheading>{{ __('Assign a subject, teacher, section, classroom, and meeting time.') }}</flux:subheading>
        </div>

        <form method="POST" action="{{ route('scheduling.class-schedules.store') }}">
            @csrf
            @include('scheduling.class-schedules.form', ['submit' => __('Create Schedule')])
        </form>
    </div>
</x-layouts::app>
