<?php

namespace Database\Factories;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Announcement> */
class AnnouncementFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'body' => fake()->paragraph(),
            'priority' => Announcement::PRIORITY_NORMAL,
            'status' => Announcement::STATUS_DRAFT,
            'publish_at' => now(),
            'expires_at' => now()->addMonth(),
            'created_by' => User::factory(),
        ];
    }
}
