<?php

namespace App\Http\Controllers\Records;

use App\Http\Controllers\Controller;
use App\Http\Requests\Records\StoreGuardianRequest;
use App\Http\Requests\Records\UpdateGuardianRequest;
use App\Models\Guardian;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GuardianController extends Controller
{
    public function index(Request $request): View
    {
        $guardians = Guardian::query()
            ->with('students')
            ->when($request->string('search')->isNotEmpty(), function ($query) use ($request): void {
                $search = $request->string('search')->toString();
                $query->where(function ($query) use ($search): void {
                    $query->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('records.guardians.index', [
            'guardians' => $guardians,
            'statuses' => [Guardian::STATUS_ACTIVE, Guardian::STATUS_INACTIVE],
        ]);
    }

    public function create(): View
    {
        abort_unless(request()->user()?->can('guardians.create'), 403);

        return view('records.guardians.create', [
            'statuses' => [Guardian::STATUS_ACTIVE, Guardian::STATUS_INACTIVE],
        ]);
    }

    public function store(StoreGuardianRequest $request): RedirectResponse
    {
        $guardian = Guardian::create($request->validated());

        return redirect()->route('records.guardians.show', $guardian)
            ->with('status', 'Guardian record created.');
    }

    public function show(Guardian $guardian): View
    {
        $guardian->load(['students.gradeLevel', 'students.section']);

        return view('records.guardians.show', [
            'guardian' => $guardian,
        ]);
    }

    public function edit(Guardian $guardian): View
    {
        abort_unless(request()->user()?->can('guardians.update'), 403);

        return view('records.guardians.edit', [
            'guardian' => $guardian,
            'statuses' => [Guardian::STATUS_ACTIVE, Guardian::STATUS_INACTIVE],
        ]);
    }

    public function update(UpdateGuardianRequest $request, Guardian $guardian): RedirectResponse
    {
        $guardian->update($request->validated());

        return redirect()->route('records.guardians.show', $guardian)
            ->with('status', 'Guardian record updated.');
    }
}
