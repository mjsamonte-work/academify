<?php

namespace App\Http\Requests\Announcements;

use App\Models\Announcement;
use App\Models\AnnouncementAudience;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('announcements.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:180'],
            'body' => ['required', 'string', 'max:5000'],
            'priority' => ['required', Rule::in([Announcement::PRIORITY_NORMAL, Announcement::PRIORITY_URGENT])],
            'status' => ['required', Rule::in([Announcement::STATUS_DRAFT, Announcement::STATUS_SCHEDULED, Announcement::STATUS_PUBLISHED, Announcement::STATUS_ARCHIVED])],
            'publish_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after:publish_at'],
            'audiences' => ['nullable', 'array'],
            'audiences.*.audience_type' => ['required_with:audiences', Rule::in([AnnouncementAudience::TYPE_EVERYONE, AnnouncementAudience::TYPE_ROLE, AnnouncementAudience::TYPE_SECTION])],
            'audiences.*.role_name' => ['nullable', Rule::exists('roles', 'name')],
            'audiences.*.section_id' => ['nullable', Rule::exists('sections', 'id')],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $audiences = collect($this->input('audiences', []))->filter(fn ($audience) => filled($audience['audience_type'] ?? null));

            if ($this->input('status') === Announcement::STATUS_PUBLISHED && $audiences->isEmpty()) {
                $validator->errors()->add('audiences', 'At least one audience is required before publishing.');
            }

            foreach ($audiences as $index => $audience) {
                if (($audience['audience_type'] ?? null) === AnnouncementAudience::TYPE_ROLE && blank($audience['role_name'] ?? null)) {
                    $validator->errors()->add("audiences.$index.role_name", 'A role audience requires a role.');
                }

                if (($audience['audience_type'] ?? null) === AnnouncementAudience::TYPE_SECTION && blank($audience['section_id'] ?? null)) {
                    $validator->errors()->add("audiences.$index.section_id", 'A section audience requires a section.');
                }
            }
        });
    }
}
