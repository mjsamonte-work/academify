<?php

namespace App\Http\Controllers\Announcements;

use App\Actions\Announcements\PublishAnnouncement;
use App\Http\Controllers\Controller;
use App\Http\Requests\Announcements\StoreAnnouncementRequest;
use App\Http\Requests\Announcements\UpdateAnnouncementRequest;
use App\Models\Announcement;
use App\Models\AnnouncementAudience;
use App\Models\Section;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class AnnouncementController extends Controller
{
    public function index(Request $request): View
    {
        $announcements = Announcement::query()
            ->with(['creator', 'audiences'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->when($request->filled('priority'), fn ($query) => $query->where('priority', $request->string('priority')->toString()))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('announcements.index', ['announcements' => $announcements, ...$this->lookups()]);
    }

    public function create(): View
    {
        abort_unless(request()->user()?->can('announcements.create'), 403);

        return view('announcements.create', $this->lookups());
    }

    public function store(StoreAnnouncementRequest $request, PublishAnnouncement $publisher): RedirectResponse
    {
        $announcement = Announcement::create([
            ...$request->safe()->except('audiences'),
            'created_by' => $request->user()?->id,
        ]);

        $this->syncAudiences($announcement, $request->input('audiences', []));

        if ($announcement->status === Announcement::STATUS_PUBLISHED) {
            $publisher->handle($announcement);
        }

        return redirect()->route('announcements.show', $announcement)->with('status', 'Announcement created.');
    }

    public function show(Announcement $announcement): View
    {
        $announcement->load(['creator', 'audiences.section', 'notifications.user']);

        return view('announcements.show', compact('announcement'));
    }

    public function edit(Announcement $announcement): View
    {
        abort_unless(request()->user()?->can('announcements.update'), 403);

        return view('announcements.edit', ['announcement' => $announcement->load('audiences'), ...$this->lookups()]);
    }

    public function update(UpdateAnnouncementRequest $request, Announcement $announcement, PublishAnnouncement $publisher): RedirectResponse
    {
        $announcement->update($request->safe()->except('audiences'));
        $this->syncAudiences($announcement, $request->input('audiences', []));

        if ($announcement->status === Announcement::STATUS_PUBLISHED) {
            $publisher->handle($announcement);
        }

        return redirect()->route('announcements.show', $announcement)->with('status', 'Announcement updated.');
    }

    private function syncAudiences(Announcement $announcement, array $audiences): void
    {
        $announcement->audiences()->delete();

        collect($audiences)
            ->filter(fn ($audience) => filled($audience['audience_type'] ?? null))
            ->each(fn ($audience) => $announcement->audiences()->create([
                'audience_type' => $audience['audience_type'],
                'role_name' => $audience['audience_type'] === AnnouncementAudience::TYPE_ROLE ? ($audience['role_name'] ?? null) : null,
                'section_id' => $audience['audience_type'] === AnnouncementAudience::TYPE_SECTION ? ($audience['section_id'] ?? null) : null,
            ]));
    }

    private function lookups(): array
    {
        return [
            'statuses' => [Announcement::STATUS_DRAFT, Announcement::STATUS_SCHEDULED, Announcement::STATUS_PUBLISHED, Announcement::STATUS_ARCHIVED],
            'priorities' => [Announcement::PRIORITY_NORMAL, Announcement::PRIORITY_URGENT],
            'audienceTypes' => [AnnouncementAudience::TYPE_EVERYONE, AnnouncementAudience::TYPE_ROLE, AnnouncementAudience::TYPE_SECTION],
            'roles' => Role::query()->orderBy('name')->get(),
            'sections' => Section::query()->with('gradeLevel')->orderBy('name')->get(),
        ];
    }
}
