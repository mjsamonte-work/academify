<?php

namespace Database\Factories;

use App\Models\Announcement;
use App\Models\AnnouncementAudience;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<AnnouncementAudience> */
class AnnouncementAudienceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'announcement_id' => Announcement::factory(),
            'audience_type' => AnnouncementAudience::TYPE_EVERYONE,
            'role_name' => null,
            'section_id' => null,
        ];
    }
}
