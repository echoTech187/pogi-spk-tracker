<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ActivityController extends Controller
{
    /**
     * Tampilkan jejak audit permanen (Audit Logs) secara live dari basis data MySQL.
     */
    public function index(Request $request): View
    {
        $logs = DB::table('audit_logs')
            ->leftJoin('users', 'audit_logs.user_id', '=', 'users.id')
            ->select(
                'audit_logs.*',
                'users.full_name as user_name',
                'users.email as user_email',
                'users.role as user_role'
            )
            ->orderBy('audit_logs.created_at', 'desc')
            ->limit(100)
            ->get();

        $totalLogs = DB::table('audit_logs')->count();
        $todayStart = Carbon::today();
        $todayActions = DB::table('audit_logs')->where('created_at', '>=', $todayStart)->count();
        $uniqueUsers = DB::table('audit_logs')->distinct('user_id')->count('user_id');
        $securityActions = DB::table('audit_logs')
            ->whereIn('action', ['REASSIGN_DOCTOR_PIC', 'KEMENKES_PORTAL_ACCESS', 'LOGIN', 'LOGOUT', 'UPDATE_STATUS'])
            ->count();

        $metrics = [
            'total_logs' => $totalLogs,
            'today_actions' => $todayActions,
            'unique_users' => $uniqueUsers,
            'security_actions' => $securityActions,
        ];

        return view('activity', [
            'logs' => $logs,
            'metrics' => $metrics,
        ]);
    }
}
