<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\AnnouncementAudience;
use App\Models\Enrollment;
use App\Models\Notification;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnnouncementNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrators_can_manage_announcements(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();

        $this->actingAs($admin)->get(route('announcements.index'))->assertOk()->assertSee('Announcements');
        $this->actingAs($admin)->get(route('announcements.create'))->assertOk()->assertSee('Create Announcement');
    }

    public function test_recipients_can_access_notices_but_not_admin_announcements(): void
    {
        $this->seed();
        $teacher = User::where('email', 'teacher@academify.local')->firstOrFail();
        $student = User::where('email', 'student@academify.local')->firstOrFail();

        $this->actingAs($teacher)->get(route('notices.index'))->assertOk()->assertSee('Notices');
        $this->actingAs($student)->get(route('notices.index'))->assertOk()->assertSee('Notices');
        $this->actingAs($teacher)->get(route('announcements.index'))->assertForbidden();
        $this->actingAs($student)->get(route('announcements.index'))->assertForbidden();
    }

    public function test_announcement_menus_are_permission_driven(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();
        $teacher = User::where('email', 'teacher@academify.local')->firstOrFail();

        $this->actingAs($admin)->get(route('dashboard'))->assertOk()->assertSee('Announcements')->assertDontSee('Notices');
        $this->actingAs($teacher)->get(route('dashboard'))->assertOk()->assertSee('Notices')->assertDontSee('Announcements');
    }

    public function test_publishing_requires_an_audience(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();

        $this->actingAs($admin)->post(route('announcements.store'), [
            'title' => 'No Audience',
            'body' => 'This should not publish.',
            'priority' => Announcement::PRIORITY_NORMAL,
            'status' => Announcement::STATUS_PUBLISHED,
        ])->assertSessionHasErrors(['audiences']);
    }

    public function test_role_audience_creates_idempotent_notifications(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();
        $teacher = User::where('email', 'teacher@academify.local')->firstOrFail();

        $payload = [
            'title' => 'Faculty Notice',
            'body' => 'Teacher audience announcement.',
            'priority' => Announcement::PRIORITY_URGENT,
            'status' => Announcement::STATUS_PUBLISHED,
            'audiences' => [
                ['audience_type' => AnnouncementAudience::TYPE_ROLE, 'role_name' => 'Teacher'],
            ],
        ];

        $response = $this->actingAs($admin)->post(route('announcements.store'), $payload);
        $announcement = Announcement::where('title', 'Faculty Notice')->firstOrFail();
        $response->assertRedirect(route('announcements.show', $announcement));

        $this->assertDatabaseHas('notifications', ['announcement_id' => $announcement->id, 'user_id' => $teacher->id]);

        $this->actingAs($admin)->put(route('announcements.update', $announcement), $payload)
            ->assertRedirect(route('announcements.show', $announcement));

        $this->assertSame(1, Notification::where('announcement_id', $announcement->id)->where('user_id', $teacher->id)->count());
    }

    public function test_section_audience_creates_notifications_for_matching_student_users(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@academify.local')->firstOrFail();
        $studentUser = User::where('email', 'student@academify.local')->firstOrFail();
        $student = Student::where('student_number', 'STU-0001')->firstOrFail();
        $student->update(['email' => $studentUser->email]);
        $section = Section::where('code', 'G1-A')->firstOrFail();

        $this->assertDatabaseHas('enrollments', [
            'student_id' => $student->id,
            'section_id' => $section->id,
            'status' => Enrollment::STATUS_ENROLLED,
        ]);

        $this->actingAs($admin)->post(route('announcements.store'), [
            'title' => 'Section Notice',
            'body' => 'Section audience announcement.',
            'priority' => Announcement::PRIORITY_NORMAL,
            'status' => Announcement::STATUS_PUBLISHED,
            'audiences' => [
                ['audience_type' => AnnouncementAudience::TYPE_SECTION, 'section_id' => $section->id],
            ],
        ]);

        $announcement = Announcement::where('title', 'Section Notice')->firstOrFail();
        $this->assertDatabaseHas('notifications', ['announcement_id' => $announcement->id, 'user_id' => $studentUser->id]);
    }

    public function test_users_can_only_view_and_read_their_own_notifications(): void
    {
        $this->seed();
        $teacher = User::where('email', 'teacher@academify.local')->firstOrFail();
        $student = User::where('email', 'student@academify.local')->firstOrFail();
        $notification = Notification::where('user_id', $teacher->id)->firstOrFail();

        $this->actingAs($student)->get(route('notices.show', $notification))->assertForbidden();

        $this->actingAs($teacher)->get(route('notices.show', $notification))->assertOk()->assertSee('Welcome to Academify');
        $this->actingAs($teacher)->post(route('notices.read', $notification))->assertRedirect(route('notices.show', $notification));

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_archived_announcements_do_not_show_as_active_notices(): void
    {
        $this->seed();
        $teacher = User::where('email', 'teacher@academify.local')->firstOrFail();
        $announcement = Announcement::where('title', 'Welcome to Academify')->firstOrFail();
        $announcement->update(['status' => Announcement::STATUS_ARCHIVED]);

        $this->actingAs($teacher)->get(route('notices.index'))->assertOk()->assertDontSee('Welcome to Academify');
    }
}
