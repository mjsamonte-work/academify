<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $key
 * @property mixed $value
 * @property string $group
 * @property string $label
 * @property string $type
 * @property int|null $updated_by
 */
#[Fillable(['key', 'value', 'group', 'label', 'type', 'updated_by'])]
class Setting extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'value' => 'array',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function displayValue(): mixed
    {
        return $this->value['value'] ?? null;
    }
}
