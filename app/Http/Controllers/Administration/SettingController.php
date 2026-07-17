<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Http\Requests\Administration\UpdateSettingsRequest;
use App\Models\GradingPeriod;
use App\Models\SchoolYear;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        return view('administration.settings.edit', [
            'settings' => $this->settings(),
            'schoolYears' => SchoolYear::query()->orderByDesc('starts_at')->get(),
            'gradingPeriods' => GradingPeriod::query()->with('schoolYear')->orderBy('sort_order')->get(),
        ]);
    }

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        abort_unless($request->user()?->can('settings.update'), 403);

        foreach ($request->validated() as $key => $value) {
            Setting::query()
                ->where('key', $key)
                ->firstOrFail()
                ->update([
                    'value' => ['value' => $value],
                    'updated_by' => $request->user()?->id,
                ]);
        }

        return redirect()->route('administration.settings.edit')->with('status', 'System settings updated.');
    }

    /**
     * @return array<string, Setting>
     */
    private function settings(): array
    {
        return Setting::query()
            ->orderBy('group')
            ->orderBy('id')
            ->get()
            ->keyBy('key')
            ->all();
    }
}
