<?php

namespace App\Models;

use App\Models\Concerns\HasActiveStatus;
use Database\Factories\TermFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $school_year_id
 * @property string $name
 * @property Carbon $starts_at
 * @property Carbon $ends_at
 * @property int $sort_order
 * @property string $status
 */
#[Fillable(['school_year_id', 'name', 'starts_at', 'ends_at', 'sort_order', 'status'])]
class Term extends Model
{
    /** @use HasFactory<TermFactory> */
    use HasFactory;

    public const STATUS_ACTIVE = HasActiveStatus::STATUS_ACTIVE;

    public const STATUS_INACTIVE = HasActiveStatus::STATUS_INACTIVE;

    /**
     * @return BelongsTo<SchoolYear, $this>
     */
    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'starts_at' => 'date',
            'ends_at' => 'date',
            'sort_order' => 'integer',
        ];
    }
}
