<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Services\ActivityLogger;
use Illuminate\Database\Eloquent\Model;

class ActivityLogObserver
{
    /**
     * @var array<int, string>
     */
    private array $ignoredAttributes = [
        'updated_at',
        'created_at',
        'remember_token',
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    public function __construct(private readonly ActivityLogger $logger) {}

    public function created(Model $model): void
    {
        $this->logger->record($model, ActivityLog::ACTION_CREATED, null, $this->filteredAttributes($model->getAttributes()));
    }

    public function updated(Model $model): void
    {
        $changes = $this->filteredAttributes($model->getChanges());

        if ($changes === []) {
            return;
        }

        $oldValues = collect($changes)
            ->mapWithKeys(fn (mixed $value, string $key): array => [$key => $model->getOriginal($key)])
            ->all();

        $this->logger->record($model, $this->actionFor($model), $oldValues, $changes);
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    private function filteredAttributes(array $attributes): array
    {
        return collect($attributes)
            ->except($this->ignoredAttributes)
            ->all();
    }

    private function actionFor(Model $model): string
    {
        if (! $model->wasChanged('status')) {
            return ActivityLog::ACTION_UPDATED;
        }

        return match ($model->getAttribute('status')) {
            'active' => ActivityLog::ACTION_ACTIVATED,
            'inactive' => ActivityLog::ACTION_DEACTIVATED,
            'submitted' => ActivityLog::ACTION_SUBMITTED,
            'published' => ActivityLog::ACTION_PUBLISHED,
            'archived' => ActivityLog::ACTION_ARCHIVED,
            'withdrawn' => ActivityLog::ACTION_WITHDRAWN,
            'completed' => ActivityLog::ACTION_COMPLETED,
            default => ActivityLog::ACTION_UPDATED,
        };
    }
}
