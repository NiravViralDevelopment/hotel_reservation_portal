<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = AuditLog::query()
            ->with('user')
            ->orderByDesc('created_at');

        if ($request->filled('module')) {
            $query->where('module', $request->string('module'));
        }

        if ($request->filled('action')) {
            $query->where('action', $request->string('action'));
        }

        $logs = $query->paginate(50)->withQueryString();
        $modules = AuditLog::query()->distinct()->orderBy('module')->pluck('module');

        return view('audit-logs.index', compact('logs', 'modules'));
    }
}
