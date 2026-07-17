<x-layouts::app :title="__('Grade Records')">
    <div class="mx-auto flex w-full max-w-7xl flex-1 flex-col gap-6 p-6">
        <div><flux:heading size="xl">{{ __('Grade Records') }}</flux:heading><flux:subheading>{{ $assessment->title }} · {{ $assessment->classSchedule->section->code }}</flux:subheading></div>
        <section class="rounded-lg border border-zinc-200 bg-white shadow-xs dark:border-zinc-700 dark:bg-zinc-900">
            <div class="overflow-x-auto"><table class="w-full text-left text-sm"><thead class="border-b border-zinc-200 text-xs uppercase text-zinc-500 dark:border-zinc-700 dark:text-zinc-400"><tr><th class="px-4 py-3">{{ __('Student') }}</th><th class="px-4 py-3">{{ __('Score') }}</th><th class="px-4 py-3">{{ __('Status') }}</th><th class="px-4 py-3">{{ __('Remarks') }}</th></tr></thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">@forelse($assessment->studentGrades as $grade)<tr><td class="px-4 py-4"><p class="font-medium">{{ $grade->student->fullName() }}</p><p class="text-zinc-500">{{ $grade->student->student_number }}</p></td><td class="px-4 py-4">{{ $grade->score ?? __('No score') }} / {{ $assessment->max_score }}</td><td class="px-4 py-4"><x-records.partials.status-badge :status="$grade->status" /></td><td class="px-4 py-4">{{ $grade->remarks ?? __('No remarks') }}</td></tr>@empty<tr><td colspan="4" class="px-4 py-10 text-center text-zinc-500">{{ __('No grade records found.') }}</td></tr>@endforelse</tbody>
            </table></div>
        </section>
    </div>
</x-layouts::app>
