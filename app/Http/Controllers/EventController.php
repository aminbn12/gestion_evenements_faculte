<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EventController extends Controller
{
    /**
     * Display a listing of the events.
     */
    public function index(Request $request)
    {
        $query = Event::with(['department', 'creator', 'users']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by department
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->where('start_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('end_date', '<=', $request->end_date);
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $events = $query->orderBy('start_date', 'desc')->paginate(15);
        $departments = Department::where('is_active', true)->get();

        return view('events.index', compact('events', 'departments'));
    }

    /**
     * Show the form for creating a new event.
     */
    public function create()
    {
        $departments = Department::where('is_active', true)->get();
        $users = User::where('status', 'active')->get();

        return view('events.create', compact('departments', 'users'));
    }

    /**
     * Store a newly created event in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_all_day' => 'boolean',
            'location' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'type' => 'required|in:conference,seminar,workshop,meeting,ceremony,other',
            'priority' => 'required|in:low,medium,high,critical',
            'status' => 'required|in:draft,published',
            'capacity' => 'nullable|integer|min:1',
            'is_public' => 'boolean',
            'requires_registration' => 'boolean',
            'reminder_days_before' => 'integer|min:0|max:30',
            'auto_reminder_enabled' => 'boolean',
            'assigned_users' => 'nullable|array',
            'assigned_users.*.user_id' => 'exists:users,id',
            'assigned_users.*.role' => 'in:organizer,presenter,participant,staff,volunteer',
        ]);

        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(6);
        $validated['created_by'] = Auth::id();

        $event = Event::create($validated);

        // Assign users
        if (!empty($validated['assigned_users'])) {
            foreach ($validated['assigned_users'] as $assignment) {
                $event->users()->attach($assignment['user_id'], [
                    'role' => $assignment['role'],
                    'status' => 'pending',
                ]);
            }
        }

        return redirect()->route('events.show', $event)
            ->with('success', __('Event created successfully.'));
    }

    /**
     * Display the specified event.
     */
    public function show(Event $event)
    {
        $event->load(['department', 'creator', 'users', 'assignments.user', 'alerts.creator']);

        return view('events.show', compact('event'));
    }

    /**
     * Show the form for editing the specified event.
     */
    public function edit(Event $event)
    {
        $departments = Department::where('is_active', true)->get();
        $users = User::where('status', 'active')->get();
        $event->load('users');

        return view('events.edit', compact('event', 'departments', 'users'));
    }

    /**
     * Update the specified event in storage.
     */
    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'department_id' => 'nullable|exists:departments,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_all_day' => 'boolean',
            'location' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'type' => 'required|in:conference,seminar,workshop,meeting,ceremony,other',
            'priority' => 'required|in:low,medium,high,critical',
            'status' => 'required|in:draft,published,cancelled,completed',
            'capacity' => 'nullable|integer|min:1',
            'is_public' => 'boolean',
            'requires_registration' => 'boolean',
            'reminder_days_before' => 'integer|min:0|max:30',
            'auto_reminder_enabled' => 'boolean',
            'assigned_users' => 'nullable|array',
            'assigned_users.*.user_id' => 'exists:users,id',
            'assigned_users.*.role' => 'in:organizer,presenter,participant,staff,volunteer',
        ]);

        $event->update($validated);

        // Sync users
        if (!empty($validated['assigned_users'])) {
            $usersToSync = [];
            foreach ($validated['assigned_users'] as $assignment) {
                $usersToSync[$assignment['user_id']] = [
                    'role' => $assignment['role'],
                ];
            }
            $event->users()->sync($usersToSync);
        }

        return redirect()->route('events.show', $event)
            ->with('success', __('Event updated successfully.'));
    }

    /**
     * Remove the specified event from storage.
     */
    public function destroy(Event $event)
    {
        $event->delete();

        return redirect()->route('events.index')
            ->with('success', __('Event deleted successfully.'));
    }

    /**
     * Publish an event.
     */
    public function publish(Event $event)
    {
        $event->update(['status' => 'published']);

        return redirect()->route('events.show', $event)
            ->with('success', __('Event published successfully.'));
    }

    /**
     * Cancel an event.
     */
    public function cancel(Event $event)
    {
        $event->update(['status' => 'cancelled']);

        return redirect()->route('events.show', $event)
            ->with('success', __('Event cancelled successfully.'));
    }

    /**
     * Assign users to an event.
     */
    public function assign(Request $request, Event $event)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:organizer,presenter,participant,staff,volunteer',
        ]);

        $event->users()->attach($validated['user_id'], [
            'role' => $validated['role'],
            'status' => 'pending',
        ]);

        return redirect()->route('events.show', $event)
            ->with('success', __('User assigned to event successfully.'));
    }

    /**
     * Get events for calendar (JSON).
     */
    public function calendar(Request $request)
    {
        $start = $request->input('start', now()->startOfMonth());
        $end = $request->input('end', now()->endOfMonth());

        $events = Event::with(['department'])
            ->whereBetween('start_date', [$start, $end])
            ->where('status', 'published')
            ->get();

        return response()->json($events->map(function ($event) {
            return [
                'id' => $event->id,
                'title' => $event->title,
                'start' => $event->start_date->toIso8601String(),
                'end' => $event->end_date->toIso8601String(),
                'allDay' => $event->is_all_day,
                'url' => route('events.show', $event),
                'backgroundColor' => $this->getPriorityColor($event->priority),
                'borderColor' => $this->getPriorityColor($event->priority),
                'extendedProps' => [
                    'location' => $event->location,
                    'type' => $event->type,
                    'priority' => $event->priority,
                ],
            ];
        }));
    }

    /**
     * Get color for priority.
     */
    protected function getPriorityColor(string $priority): string
    {
        return match ($priority) {
            'low' => '#28a745',
            'medium' => '#ffc107',
            'high' => '#dc3545',
            'critical' => '#1a3c5e',
            default => '#6c757d',
        };
    }
}
