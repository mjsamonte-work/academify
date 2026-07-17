<x-layouts::app :title="__('Create Announcement')">
    <div class="mx-auto flex w-full max-w-5xl flex-1 flex-col gap-6 p-6">
        <div><flux:heading size="xl">{{ __('Create Announcement') }}</flux:heading><flux:subheading>{{ __('Publish a formal notice to selected audiences.') }}</flux:subheading></div>
        <form method="POST" action="{{ route('announcements.store') }}">@csrf @include('announcements.form', ['submit' => __('Create Announcement')])</form>
    </div>
</x-layouts::app>
