<?php

namespace App\Http\Requests\Announcements;

class UpdateAnnouncementRequest extends StoreAnnouncementRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('announcements.update') ?? false;
    }
}
