<x-layouts::app :title="__('Edit Announcement')">
    <div class="mx-auto flex w-full max-w-5xl flex-1 flex-col gap-6 p-6">
        <div><flux:heading size="xl">{{ __('Edit Announcement') }}</flux:heading><flux:subheading>{{ $announcement->title }}</flux:subheading></div>
        <form method="POST" action="{{ route('announcements.update', $announcement) }}">@csrf @method('PUT') @include('announcements.form', ['submit' => __('Save Announcement')])</form>
    </div>
</x-layouts::app>
