<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Http\Requests\Administration\ActivityLogFilterRequest;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(ActivityLogFilterRequest $request): View
    {
        $filters = $request->validated();

        $activityLogs = ActivityLog::query()
            ->with('user')
            ->when($filters['module'] ?? null, fn ($query, $module) => $query->where('module', $module))
            ->when($filters['action'] ?? null, fn ($query, $action) => $query->where('action', $action))
            ->when($filters['user_id'] ?? null, fn ($query, $userId) => $query->where('user_id', $userId))
            ->when($filters['search'] ?? null, function ($query, $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('subject_label', 'like', "%{$search}%")
                        ->orWhere('subject_type', 'like', "%{$search}%");
                });
            })
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '<=', $date))
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('administration.audit-logs.index', [
            'activityLogs' => $activityLogs,
            'modules' => ActivityLog::modules(),
            'actions' => ActivityLog::actions(),
            'users' => User::query()->orderBy('name')->get(),
        ]);
    }

    public function show(ActivityLog $activityLog): View
    {
        return view('administration.audit-logs.show', [
            'activityLog' => $activityLog->load('user'),
        ]);
    }
}
