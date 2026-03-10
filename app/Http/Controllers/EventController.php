<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Department;
use App\Models\User;
use App\Models\Alert;
use App\Models\Role;
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
        $users = User::where('status', 'active')->with('role', 'department')->get();
        $roles = Role::all();

        return view('events.create', compact('departments', 'users', 'roles'));
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
            'status' => 'required|in:draft,published,cancelled,completed',
            'capacity' => 'nullable|integer|min:1',
            'is_public' => 'boolean',
            'requires_registration' => 'boolean',
            'reminder_days_before' => 'integer|min:0|max:30',
            'auto_reminder_enabled' => 'boolean',
            'assigned_users' => 'nullable|array',
            'assigned_users.*.user_id' => 'exists:users,id',
            'assigned_users.*.role' => 'in:organizer,presenter,participant,staff,volunteer',
            // Alert fields
            'recipient_type' => 'nullable|in:all,role,department,users',
            'selected_roles' => 'nullable|array',
            'selected_roles.*' => 'exists:roles,id',
            'alert_department_id' => 'nullable|exists:departments,id',
            'selected_users' => 'nullable',
            'send_email_alert' => 'boolean',
            'selected_users_list' => 'nullable|string', // From Paramètres section
        ]);

        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(6);
        $validated['created_by'] = Auth::id();

        $event = Event::create($validated);

        // Assign users from selected_users_list (Paramètres section)
        $selectedUsersList = $request->input('selected_users_list', '');
        if ($selectedUsersList) {
            $userIds = array_map('intval', explode(',', $selectedUsersList));
            foreach ($userIds as $userId) {
                $event->users()->attach($userId, [
                    'role' => 'participant',
                    'status' => 'pending',
                ]);
            }
        }

        // Assign users from assigned_users (if any)
        if (!empty($validated['assigned_users'])) {
            foreach ($validated['assigned_users'] as $assignment) {
                $event->users()->attach($assignment['user_id'], [
                    'role' => $assignment['role'],
                    'status' => 'pending',
                ]);
            }
        }

        // Create alert based on recipient type
        $this->createEventAlert($event, $request);

        // Notify admins about new event
        $this->notifyAdmins($event);

        // Create automatic reminder alerts if enabled
        if ($event->auto_reminder_enabled && $event->reminder_days_before > 0) {
            $this->createAutoReminders($event);
        }

        return redirect()->route('events.show', $event)
            ->with('success', __('Event created successfully.'));
    }

    /**
     * Create alert based on recipient selection.
     */
    protected function createEventAlert(Event $event, Request $request): void
    {
        $recipientType = $request->input('recipient_type', 'all');
        $sendEmail = $request->input('send_email_alert', false);

        if (!$sendEmail) {
            return;
        }

        $customRecipients = null;
        $departmentId = null;

        switch ($recipientType) {
            case 'role':
                $selectedRoles = $request->input('selected_roles', []);
                if (!empty($selectedRoles)) {
                    $users = User::whereHas('role', function ($query) use ($selectedRoles) {
                        $query->whereIn('id', $selectedRoles);
                    })->get();
                    $customRecipients = $users->pluck('id')->toArray();
                }
                break;
            case 'department':
                $departmentId = $request->input('alert_department_id');
                break;
            case 'users':
                $selectedUsers = $request->input('selected_users', '');
                if ($selectedUsers) {
                    // Handle both array and string formats
                    if (is_array($selectedUsers)) {
                        $customRecipients = array_map('intval', $selectedUsers);
                    } else {
                        $customRecipients = array_map('intval', explode(',', $selectedUsers));
                    }
                }
                break;
        }

        Alert::create([
            'event_id' => $event->id,
            'created_by' => Auth::id(),
            'subject' => 'Nouvel événement: ' . $event->title,
            'message' => 'Un nouvel événement a été créé.',
            'send_email' => true,
            'send_whatsapp' => false,
            'recipient_type' => $recipientType,
            'custom_recipients' => $customRecipients,
            'department_id' => $departmentId,
            'scheduled_at' => now(),
            'status' => 'pending',
        ]);
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
        $users = User::where('status', 'active')->with('role', 'department')->get();
        $roles = Role::all();
        $event->load('users');

        return view('events.edit', compact('event', 'departments', 'users', 'roles'));
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
            'edit_selected_users_list' => 'nullable|string',
        ]);

        $event->update($validated);

        // Handle selected users from edit form
        $selectedUsersList = $request->input('edit_selected_users_list', '');
        if ($selectedUsersList) {
            $userIds = array_map('intval', explode(',', $selectedUsersList));
            $existingUserIds = $event->users->pluck('id')->toArray();
            
            foreach ($userIds as $userId) {
                if (!in_array($userId, $existingUserIds)) {
                    $event->users()->attach($userId, [
                        'role' => 'participant',
                        'status' => 'pending',
                    ]);
                }
            }
        }

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
            'user_ids' => 'required',
            'role' => 'required|in:organizer,presenter,participant,staff,volunteer,speaker',
        ]);

        // Handle comma-separated user IDs
        $userIds = $validated['user_ids'];
        if (is_string($userIds)) {
            $userIds = array_map('intval', explode(',', $userIds));
        }

        // Filter out empty values
        $userIds = array_filter($userIds);

        // Get existing user IDs for this event
        $existingUserIds = $event->users->pluck('id')->toArray();

        foreach ($userIds as $userId) {
            // Only attach if user is not already assigned
            if (!in_array($userId, $existingUserIds)) {
                $event->users()->attach($userId, [
                    'role' => $validated['role'],
                    'status' => 'pending',
                ]);
            }
        }

        return redirect()->route('events.show', $event)
            ->with('success', __('User(s) assigned to event successfully.'));
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

    /**
     * Notify admins when a new event is created.
     */
    protected function notifyAdmins(Event $event): void
    {
        // Get all manager users (using role_id relationship)
        $admins = User::whereHas('role', function ($query) {
            $query->where('slug', 'manager');
        })->get();

        if ($admins->isEmpty()) {
            return;
        }

        // Create alert for admins
        Alert::create([
            'event_id' => $event->id,
            'created_by' => Auth::id(),
            'subject' => 'Nouvel événement créé: ' . $event->title,
            'message' => 'Un nouvel événement "' . $event->title . '" a été créé.\n\n' .
                        'Date: ' . $event->start_date->format('d/m/Y H:i') . '\n' .
                        'Lieu: ' . ($event->location ?? 'Non défini') . '\n' .
                        'Type: ' . $event->type . '\n' .
                        'Priorité: ' . $event->priority . '\n\n' .
                        'Créé par: ' . Auth::user()->name,
            'send_email' => true,
            'send_whatsapp' => false,
            'recipient_type' => 'role',
            'custom_recipients' => $admins->pluck('id')->toArray(),
            'scheduled_at' => now(),
            'status' => 'pending',
        ]);
    }

    /**
     * Create automatic reminder alerts for an event.
     */
    protected function createAutoReminders(Event $event): void
    {
        // Configurable reminder days: 1 month, 10 days, 1 day
        $reminderDays = [30, 10, 1];
        
        // Or use the event's reminder_days_before setting
        if ($event->reminder_days_before > 0) {
            $reminderDays = [$event->reminder_days_before];
        }

        foreach ($reminderDays as $days) {
            // Check if alert already exists for this reminder
            $exists = Alert::where('event_id', $event->id)
                ->where('reminder_days', $days)
                ->exists();

            if (!$exists) {
                $scheduledDate = $event->start_date->subDays($days);

                Alert::create([
                    'event_id' => $event->id,
                    'created_by' => Auth::id(),
                    'subject' => 'Rappel: ' . $event->title . ' dans ' . $days . ' jour(s)',
                    'message' => 'L\'événement "' . $event->title . '" commence dans ' . $days . ' jour(s).\n\n' .
                                'Date: ' . $event->start_date->format('d/m/Y H:i') . '\n' .
                                'Lieu: ' . ($event->location ?? 'Non défini'),
                    'send_email' => true,
                    'send_whatsapp' => false,
                    'recipient_type' => 'role',
                    'custom_recipients' => null, // Will send to all relevant users
                    'scheduled_at' => $scheduledDate,
                    'reminder_days' => $days,
                    'status' => 'pending',
                ]);
            }
        }
    }
}
