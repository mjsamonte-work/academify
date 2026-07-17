<?php

namespace App\Actions\Announcements;

use App\Models\Announcement;
use App\Models\AnnouncementAudience;
use App\Models\Enrollment;
use App\Models\Guardian;
use App\Models\Notification;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Collection;

class PublishAnnouncement
{
    public function handle(Announcement $announcement): int
    {
        $announcement->load('audiences');

        $users = $announcement->audiences
            ->flatMap(fn (AnnouncementAudience $audience) => $this->usersForAudience($audience))
            ->unique('id')
            ->values();

        foreach ($users as $user) {
            Notification::firstOrCreate([
                'user_id' => $user->id,
                'announcement_id' => $announcement->id,
            ]);
        }

        return $users->count();
    }

    /**
     * @return Collection<int, User>
     */
    private function usersForAudience(AnnouncementAudience $audience): Collection
    {
        return match ($audience->audience_type) {
            AnnouncementAudience::TYPE_EVERYONE => User::query()->where('status', User::STATUS_ACTIVE)->get(),
            AnnouncementAudience::TYPE_ROLE => User::role($audience->role_name)->where('status', User::STATUS_ACTIVE)->get(),
            AnnouncementAudience::TYPE_SECTION => $this->sectionUsers($audience),
            default => collect(),
        };
    }

    /**
     * @return Collection<int, User>
     */
    private function sectionUsers(AnnouncementAudience $audience): Collection
    {
        if (! $audience->section_id) {
            return collect();
        }

        $studentEmails = Enrollment::query()
            ->where('section_id', $audience->section_id)
            ->where('status', Enrollment::STATUS_ENROLLED)
            ->pluck('student_id')
            ->pipe(fn ($ids) => Student::query()->whereIn('id', $ids)->whereNotNull('email')->pluck('email'));

        $guardianEmails = Guardian::query()
            ->whereHas('students', fn ($query) => $query->whereIn('students.id', Enrollment::query()
                ->where('section_id', $audience->section_id)
                ->where('status', Enrollment::STATUS_ENROLLED)
                ->select('student_id')))
            ->whereNotNull('email')
            ->pluck('email');

        return User::query()
            ->whereIn('email', $studentEmails->merge($guardianEmails)->unique()->values())
            ->where('status', User::STATUS_ACTIVE)
            ->get();
    }
}
