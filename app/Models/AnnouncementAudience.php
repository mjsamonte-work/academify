<?php

namespace App\Models;

use Database\Factories\AnnouncementAudienceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['announcement_id', 'audience_type', 'role_name', 'section_id'])]
class AnnouncementAudience extends Model
{
    /** @use HasFactory<AnnouncementAudienceFactory> */
    use HasFactory;

    public const TYPE_EVERYONE = 'everyone';

    public const TYPE_ROLE = 'role';

    public const TYPE_SECTION = 'section';

    public function announcement(): BelongsTo
    {
        return $this->belongsTo(Announcement::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }
}
