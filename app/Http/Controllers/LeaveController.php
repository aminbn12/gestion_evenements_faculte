<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    /**
     * Display a listing of the leaves.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $canApprove = $user->hasRole('manager') || $user->hasRole('chef-dept');

        if ($canApprove) {
            $query = Leave::with(['user.department']);
        } else {
            $query = Leave::where('user_id', $user->id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->where('start_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('end_date', '<=', $request->end_date);
        }

        $leaves = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('leaves.index', compact('leaves', 'canApprove'));
    }

    /**
     * Show the form for creating a new leave.
     */
    public function create()
    {
        return view('leaves.create');
    }

    /**
     * Store a newly created leave in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:annual,sick,maternity,paternity,study,unpaid,other',
            'reason' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_half_day' => 'boolean',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // Calculate days count
        $startDate = \Carbon\Carbon::parse($validated['start_date']);
        $endDate = \Carbon\Carbon::parse($validated['end_date']);
        $daysCount = $startDate->diffInDays($endDate) + 1;

        if ($validated['is_half_day'] ?? false) {
            $daysCount = 0.5;
        }

        $validated['user_id'] = Auth::id();
        $validated['days_count'] = $daysCount;
        $validated['status'] = 'pending';

        // Handle attachments
        if ($request->hasFile('attachments')) {
            $attachments = [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('leaves/' . Auth::id(), 'public');
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                ];
            }
            $validated['attachments'] = $attachments;
        }

        Leave::create($validated);

        return redirect()->route('leaves.index')
            ->with('success', __('Leave request submitted successfully.'));
    }

    /**
     * Display the specified leave.
     */
    public function show(Leave $leave)
    {
        $leave->load(['user', 'approver']);

        return view('leaves.show', compact('leave'));
    }

    /**
     * Approve the specified leave.
     */
    public function approve(Request $request, Leave $leave)
    {
        if ($leave->status !== 'pending') {
            return redirect()->route('leaves.index')
                ->with('error', __('Only pending leaves can be approved.'));
        }

        $leave->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('leaves.index')
            ->with('success', __('Leave approved successfully.'));
    }

    /**
     * Reject the specified leave.
     */
    public function reject(Request $request, Leave $leave)
    {
        if ($leave->status !== 'pending') {
            return redirect()->route('leaves.index')
                ->with('error', __('Only pending leaves can be rejected.'));
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $leave->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return redirect()->route('leaves.index')
            ->with('success', __('Leave rejected.'));
    }

    /**
     * Cancel the specified leave.
     */
    public function cancel(Leave $leave)
    {
        if ($leave->user_id !== Auth::id()) {
            abort(403);
        }

        if ($leave->status !== 'pending') {
            return redirect()->route('leaves.index')
                ->with('error', __('Only pending leaves can be cancelled.'));
        }

        $leave->update(['status' => 'cancelled']);

        return redirect()->route('leaves.index')
            ->with('success', __('Leave cancelled.'));
    }
}
