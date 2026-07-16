<?php

namespace App\Http\Controllers\Records;

use App\Http\Controllers\Controller;
use App\Http\Requests\Records\StoreGuardianStudentRequest;
use App\Models\Guardian;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;

class StudentGuardianController extends Controller
{
    public function store(StoreGuardianStudentRequest $request, Student $student): RedirectResponse
    {
        $validated = $this->normalized($request->validated());

        $this->clearPrimaryContactIfNeeded($student, $validated);
        $student->guardians()->syncWithoutDetaching([
            $validated['guardian_id'] => collect($validated)->except('guardian_id')->all(),
        ]);

        return redirect()->route('records.students.show', $student)
            ->with('status', 'Guardian relationship saved.');
    }

    public function update(StoreGuardianStudentRequest $request, Student $student, Guardian $guardian): RedirectResponse
    {
        $validated = $this->normalized($request->validated());
        unset($validated['guardian_id']);

        $this->clearPrimaryContactIfNeeded($student, $validated);
        $student->guardians()->updateExistingPivot($guardian->id, $validated);

        return redirect()->route('records.students.show', $student)
            ->with('status', 'Guardian relationship updated.');
    }

    public function destroy(Student $student, Guardian $guardian): RedirectResponse
    {
        abort_unless(request()->user()?->can('students.update'), 403);

        $student->guardians()->detach($guardian->id);

        return redirect()->route('records.students.show', $student)
            ->with('status', 'Guardian relationship removed.');
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function normalized(array $validated): array
    {
        return [
            ...$validated,
            'is_primary_contact' => (bool) ($validated['is_primary_contact'] ?? false),
            'can_pick_up' => (bool) ($validated['can_pick_up'] ?? false),
            'receives_notifications' => (bool) ($validated['receives_notifications'] ?? false),
        ];
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function clearPrimaryContactIfNeeded(Student $student, array $validated): void
    {
        if (! ($validated['is_primary_contact'] ?? false)) {
            return;
        }

        $student->guardians()->newPivotStatement()
            ->where('student_id', $student->id)
            ->update(['is_primary_contact' => false]);
    }
}
