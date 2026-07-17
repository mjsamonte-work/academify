@php
    $announcement ??= null;
    $existingAudiences = collect(old('audiences', $announcement?->audiences?->map(fn ($audience) => [
        'audience_type' => $audience->audience_type,
        'role_name' => $audience->role_name,
        'section_id' => $audience->section_id,
    ])->all() ?? [['audience_type' => 'everyone']]))->pad(3, []);
@endphp
<section class="rounded-lg border border-zinc-200 bg-white p-5 shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
    <div class="grid gap-5 md:grid-cols-2">
        <div class="md:col-span-2"><label class="text-sm font-medium" for="title">{{ __('Title') }}</label><input id="title" name="title" value="{{ old('title', $announcement->title ?? '') }}" required class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">@error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
        <div class="md:col-span-2"><label class="text-sm font-medium" for="body">{{ __('Body') }}</label><textarea id="body" name="body" rows="6" required class="mt-1 w-full rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950">{{ old('body', $announcement->body ?? '') }}</textarea>@error('body')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
        <div><label class="text-sm font-medium" for="priority">{{ __('Priority') }}</label><select id="priority" name="priority" class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">@foreach($priorities as $priority)<option value="{{ $priority }}" @selected(old('priority', $announcement->priority ?? 'normal') === $priority)>{{ str($priority)->headline() }}</option>@endforeach</select></div>
        <div><label class="text-sm font-medium" for="status">{{ __('Status') }}</label><select id="status" name="status" class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">@foreach($statuses as $status)<option value="{{ $status }}" @selected(old('status', $announcement->status ?? 'draft') === $status)>{{ str($status)->headline() }}</option>@endforeach</select>@error('audiences')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
        <div><label class="text-sm font-medium" for="publish_at">{{ __('Publish At') }}</label><input id="publish_at" name="publish_at" type="datetime-local" value="{{ old('publish_at', optional($announcement?->publish_at ?? null)->format('Y-m-d\\TH:i')) }}" class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950"></div>
        <div><label class="text-sm font-medium" for="expires_at">{{ __('Expires At') }}</label><input id="expires_at" name="expires_at" type="datetime-local" value="{{ old('expires_at', optional($announcement?->expires_at ?? null)->format('Y-m-d\\TH:i')) }}" class="mt-1 h-10 w-full rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950">@error('expires_at')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror</div>
    </div>
    <div class="mt-6 border-t border-zinc-200 pt-5 dark:border-zinc-700">
        <h2 class="font-semibold">{{ __('Audiences') }}</h2>
        <div class="mt-3 grid gap-3">
            @foreach($existingAudiences as $index => $audience)
                <div class="grid gap-3 md:grid-cols-3">
                    <select name="audiences[{{ $index }}][audience_type]" class="h-10 rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950"><option value="">{{ __('No audience') }}</option>@foreach($audienceTypes as $type)<option value="{{ $type }}" @selected(($audience['audience_type'] ?? '') === $type)>{{ str($type)->headline() }}</option>@endforeach</select>
                    <select name="audiences[{{ $index }}][role_name]" class="h-10 rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950"><option value="">{{ __('Role') }}</option>@foreach($roles as $role)<option value="{{ $role->name }}" @selected(($audience['role_name'] ?? '') === $role->name)>{{ $role->name }}</option>@endforeach</select>
                    <select name="audiences[{{ $index }}][section_id]" class="h-10 rounded-md border border-zinc-300 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-950"><option value="">{{ __('Section') }}</option>@foreach($sections as $section)<option value="{{ $section->id }}" @selected((int) ($audience['section_id'] ?? 0) === $section->id)>{{ $section->code }}</option>@endforeach</select>
                </div>
            @endforeach
        </div>
    </div>
    <div class="mt-6 flex gap-3"><flux:button type="submit" variant="primary">{{ $submit }}</flux:button><flux:button :href="route('announcements.index')" wire:navigate>{{ __('Cancel') }}</flux:button></div>
</section>
