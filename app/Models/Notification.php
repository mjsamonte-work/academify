<?php

namespace App\Models;

use Database\Factories\NotificationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int $announcement_id
 * @property Carbon|null $read_at
 */
#[Fillable(['user_id', 'announcement_id', 'read_at'])]
class Notification extends Model
{
    /** @use HasFactory<NotificationFactory> */
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function announcement(): BelongsTo
    {
        return $this->belongsTo(Announcement::class);
    }

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }
}
