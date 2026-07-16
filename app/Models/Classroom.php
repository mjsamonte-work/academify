<?php

namespace App\Models;

use App\Models\Concerns\HasActiveStatus;
use Database\Factories\ClassroomFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $code
 * @property int|null $capacity
 * @property string|null $location
 * @property string $status
 */
#[Fillable(['name', 'code', 'capacity', 'location', 'status'])]
class Classroom extends Model
{
    /** @use HasFactory<ClassroomFactory> */
    use HasFactory;

    public const STATUS_ACTIVE = HasActiveStatus::STATUS_ACTIVE;

    public const STATUS_INACTIVE = HasActiveStatus::STATUS_INACTIVE;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
        ];
    }
}
