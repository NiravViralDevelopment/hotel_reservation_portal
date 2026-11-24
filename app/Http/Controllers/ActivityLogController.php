<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use App\Support\ActivityDescription;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user?->can('activity.view') || $user?->can('activity.view_all'), 403);

        $canViewAll = (bool) $user?->can('activity.view_all');

        $query = AuditLog::query()->with('user');

        if (! $canViewAll) {
            $query->where('user_id', $user->id);
        } elseif ($request->filled('user_id')) {
            $query->where('user_id', $request->integer('user_id'));
        }

        if ($request->filled('module')) {
            $query->where('module', $request->string('module')->toString());
        }

        if ($request->filled('action')) {
            $query->where('action', $request->string('action')->toString());
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date('date_to'));
        }

        if ($request->filled('q')) {
            $term = '%'.$request->string('q')->trim().'%';
            $query->where(function ($builder) use ($term): void {
                $builder->where('details', 'like', $term)
                    ->orWhere('action', 'like', $term)
                    ->orWhere('module', 'like', $term)
                    ->orWhereHas('user', function ($userQuery) use ($term): void {
                        $userQuery->where('name', 'like', $term)
                            ->orWhere('email', 'like', $term);
                    });
            });
        }

        $logs = $query
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $logs->getCollection()->transform(function (AuditLog $log) {
            $log->setAttribute('description', ActivityDescription::for($log));
            $log->setAttribute('tone', ActivityDescription::tone((string) $log->action));
            $log->setAttribute('icon', ActivityDescription::icon((string) $log->action));
            $log->setAttribute('module_label', ActivityDescription::moduleLabel((string) $log->module));

            return $log;
        });

        $modules = AuditLog::query()
            ->when(! $canViewAll, fn ($q) => $q->where('user_id', $user->id))
            ->distinct()
            ->orderBy('module')
            ->pluck('module');

        $actions = AuditLog::query()
            ->when(! $canViewAll, fn ($q) => $q->where('user_id', $user->id))
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        $users = $canViewAll
            ? User::query()->orderBy('name')->get(['id', 'name', 'email'])
            : collect();

        return view('activity-logs.index', [
            'logs' => $logs,
            'modules' => $modules,
            'actions' => $actions,
            'users' => $users,
            'canViewAll' => $canViewAll,
        ]);
    }
}
