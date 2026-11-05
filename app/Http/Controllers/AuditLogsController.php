<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = AuditLog::with('user');

        if ($request->filled('user_uid')) {
            $query->where('user_uid', $request->user_uid);
        }

        if ($request->filled('action_type')) {
            $query->where('action_type', $request->action_type);
        }

        if ($request->filled('entity_type')) {
            $query->where('entity_type', $request->entity_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('action_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('action_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        $logs = $query->orderBy('action_date', 'desc')->paginate(100);
        $users = User::where('is_active', true)->orderBy('full_name')->get();
        
        $actionTypes = AuditLog::distinct()->pluck('action_type');
        $tableNames = AuditLog::distinct()->pluck('entity_type');

        return view('audit-logs.index', compact('logs', 'users', 'actionTypes', 'tableNames'));
    }

    public function show($uid)
    {
        $log = AuditLog::with('user')->findOrFail($uid);
        return view('audit-logs.show', compact('log'));
    }
}
