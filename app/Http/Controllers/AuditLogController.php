<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Support\QuerySort;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('audit.view');

        $query = AuditLog::query()
            ->with('user');

        if ($request->filled('module')) {
            $query->where('module', $request->string('module'));
        }

        if ($request->filled('action')) {
            $query->where('action', $request->string('action'));
        }

        QuerySort::apply($query, $request, [
            'created_at' => 'created_at',
            'action' => 'action',
            'module' => 'module',
        ], 'created_at', 'desc');

        $logs = $query->paginate(10)->withQueryString();
        $modules = AuditLog::query()->distinct()->orderBy('module')->pluck('module');

        return view('audit-logs.index', compact('logs', 'modules'));
    }
}
