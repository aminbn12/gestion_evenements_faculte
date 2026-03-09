<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use App\Models\Leave;
use App\Models\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        $user = Auth::user();

        // Check if user has access to full dashboard
        $hasFullAccess = $user->hasRole('manager') || $user->hasRole('chef-dept');

        if ($hasFullAccess) {
            return $this->managerDashboard($user);
        }

        return $this->userDashboard($user);
    }

    /**
     * Display the manager dashboard.
     */
    protected function managerDashboard($user)
    {
        // Statistics
        $stats = [
            'total_events' => Event::count(),
            'upcoming_events' => Event::where('start_date', '>', now())->count(),
            'total_users' => User::where('status', 'active')->count(),
            'pending_leaves' => Leave::where('status', 'pending')->count(),
        ];

        // Upcoming events
        $upcomingEvents = Event::with(['department', 'creator'])
            ->where('start_date', '>', now())
            ->where('status', 'published')
            ->orderBy('start_date')
            ->take(5)
            ->get();

        // Recent alerts
        $recentAlerts = Alert::with(['event', 'creator'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Pending leaves
        $pendingLeaves = Leave::with('user')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Events by status
        $eventsByStatus = Event::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Events by type
        $eventsByType = Event::selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        // Events by priority
        $eventsByPriority = Event::selectRaw('priority, count(*) as count')
            ->groupBy('priority')
            ->pluck('count', 'priority')
            ->toArray();

        return view('dashboard.index', compact(
            'stats',
            'upcomingEvents',
            'recentAlerts',
            'pendingLeaves',
            'eventsByStatus',
            'eventsByType',
            'eventsByPriority'
        ));
    }

    /**
     * Display the user dashboard.
     */
    protected function userDashboard($user)
    {
        // User's upcoming events
        $upcomingEvents = $user->events()
            ->where('start_date', '>', now())
            ->where('status', 'published')
            ->orderBy('start_date')
            ->take(5)
            ->get();

        // User's leaves
        $leaves = $user->leaves()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('dashboard.user', compact('upcomingEvents', 'leaves'));
    }
}
