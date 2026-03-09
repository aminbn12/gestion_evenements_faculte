<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Department;
use App\Models\Event;
use App\Models\User;
use App\Services\AlertService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlertController extends Controller
{
    protected AlertService $alertService;

    public function __construct(AlertService $alertService)
    {
        $this->alertService = $alertService;
    }

    /**
     * Display a listing of the alerts.
     */
    public function index(Request $request)
    {
        $query = Alert::with(['event', 'creator']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by event
        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        $alerts = $query->orderBy('created_at', 'desc')->paginate(15);
        $events = Event::where('status', 'published')->get();

        return view('alerts.index', compact('alerts', 'events'));
    }

    /**
     * Show the form for creating a new alert.
     */
    public function create(Request $request)
    {
        $events = Event::where('status', 'published')->get();
        $users = User::where('status', 'active')->with(['role', 'department'])->get();
        $departments = Department::where('is_active', true)->get();
        $selectedEvent = $request->filled('event_id') ? Event::find($request->event_id) : null;

        return view('alerts.create', compact('events', 'users', 'departments', 'selectedEvent'));
    }

    /**
     * Store a newly created alert in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'nullable|exists:events,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:email,whatsapp,both',
            'recipients' => 'required_if:all_users,|array',
            'recipients.*' => 'exists:users,id',
            'all_users' => 'nullable|boolean',
            'by_department' => 'nullable|boolean',
            'department_id' => 'nullable|exists:departments,id',
            'scheduled_at' => 'nullable|date',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['status'] = $validated['scheduled_at'] ? 'scheduled' : 'sent';

        // Determine recipient type
        if (!empty($validated['all_users'])) {
            $validated['recipient_type'] = 'all';
        } elseif (!empty($validated['by_department']) && !empty($validated['department_id'])) {
            $validated['recipient_type'] = 'department';
            $validated['department_id'] = $validated['department_id'];
        } else {
            $validated['recipient_type'] = 'custom';
            $validated['custom_recipients'] = $validated['recipients'] ?? [];
        }

        // Set send options based on type
        $validated['send_email'] = in_array($validated['type'], ['email', 'both']);
        $validated['send_whatsapp'] = in_array($validated['type'], ['whatsapp', 'both']);

        $alert = Alert::create($validated);

        // If not scheduled, send immediately
        if (!$validated['scheduled_at']) {
            $this->alertService->sendAlert($alert);
        }

        return redirect()->route('alerts.show', $alert)
            ->with('success', __('Alerte créée avec succès.'));
    }

    /**
     * Display the specified alert.
     */
    public function show(Alert $alert)
    {
        $alert->load(['event', 'creator', 'logs.user']);

        return view('alerts.show', compact('alert'));
    }

    /**
     * Send the alert immediately.
     */
    public function send(Alert $alert)
    {
        if ($alert->status !== 'draft') {
            return redirect()->route('alerts.show', $alert)
                ->with('error', __('Only draft alerts can be sent.'));
        }

        $this->alertService->sendAlert($alert);

        return redirect()->route('alerts.show', $alert)
            ->with('success', __('Alert sent successfully.'));
    }

    /**
     * Cancel a scheduled alert.
     */
    public function cancel(Alert $alert)
    {
        if ($alert->status !== 'scheduled') {
            return redirect()->route('alerts.show', $alert)
                ->with('error', __('Only scheduled alerts can be cancelled.'));
        }

        $alert->update(['status' => 'draft', 'scheduled_at' => null]);

        return redirect()->route('alerts.show', $alert)
            ->with('success', __('Alert cancelled successfully.'));
    }

    /**
     * Remove the specified alert from storage.
     */
    public function destroy(Alert $alert)
    {
        if ($alert->status === 'sending') {
            return redirect()->route('alerts.index')
                ->with('error', __('Cannot delete an alert that is being sent.'));
        }

        $alert->delete();

        return redirect()->route('alerts.index')
            ->with('success', __('Alert deleted successfully.'));
    }

    /**
     * Display alert logs.
     */
    public function logs(Request $request)
    {
        $query = \App\Models\AlertLog::with(['alert.event', 'user']);

        // Filter by channel
        if ($request->filled('channel')) {
            $query->where('channel', $request->channel);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->where('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('created_at', '<=', $request->end_date);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('alerts.logs', compact('logs'));
    }

    /**
     * Get recipients for an event (AJAX).
     */
    public function getRecipients(Request $request, Event $event)
    {
        $type = $request->input('type', 'all');

        $recipients = match ($type) {
            'all' => $event->users,
            'organizers' => $event->organizers,
            'presenters' => $event->presenters,
            'participants' => $event->participants,
            default => collect(),
        };

        return response()->json($recipients->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->full_name,
                'email' => $user->email,
                'phone' => $user->phone,
            ];
        }));
    }
}
