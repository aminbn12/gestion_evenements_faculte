<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Models\TeamGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    /**
     * Display the team directory.
     */
    public function index(Request $request)
    {
        $query = User::with(['role', 'department', 'profile'])
            ->where('status', 'active');

        // Filter by department
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Filter by role
        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                    ->orWhere('last_name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $members = $query->orderBy('first_name')->paginate(20);
        $departments = Department::where('is_active', true)->get();

        return view('team.index', compact('members', 'departments'));
    }

    /**
     * Display the organizational chart.
     */
    public function orgchart()
    {
        $departments = Department::with(['manager', 'users.role'])
            ->where('is_active', true)
            ->get();

        // Build org chart data
        $orgData = $this->buildOrgChartData($departments);

        return view('team.orgchart', compact('orgData'));
    }

    /**
     * Build org chart data structure.
     */
    protected function buildOrgChartData($departments)
    {
        $data = [];

        // Get manager (top level)
        $manager = User::whereHas('role', function ($q) {
            $q->where('slug', 'manager');
        })->first();

        if ($manager) {
            $data = [
                'id' => $manager->id,
                'name' => $manager->full_name,
                'title' => 'Manager',
                'email' => $manager->email,
                'avatar' => $manager->avatar_url,
                'children' => [],
            ];

            // Add departments under manager
            foreach ($departments as $department) {
                $deptNode = [
                    'id' => 'dept-' . $department->id,
                    'name' => $department->name,
                    'title' => 'Department',
                    'children' => [],
                ];

                // Add department manager
                if ($department->manager) {
                    $deptNode['children'][] = [
                        'id' => $department->manager->id,
                        'name' => $department->manager->full_name,
                        'title' => 'Chef de Département',
                        'email' => $department->manager->email,
                        'avatar' => $department->manager->avatar_url,
                        'children' => [],
                    ];
                }

                $data['children'][] = $deptNode;
            }
        }

        return $data;
    }

    /**
     * Display team groups.
     */
    public function groups()
    {
        $groups = TeamGroup::with(['leader', 'department', 'members'])
            ->where('is_active', true)
            ->get();

        return view('team.groups', compact('groups'));
    }

    /**
     * Show the form for creating a new team group.
     */
    public function createGroup()
    {
        $users = User::where('status', 'active')->with(['role', 'department'])->get();
        $departments = Department::where('is_active', true)->get();

        return view('team.create', compact('users', 'departments'));
    }

    /**
     * Show the form for editing a team group.
     */
    public function editGroup(TeamGroup $group)
    {
        $users = User::where('status', 'active')->with(['role', 'department'])->get();
        $departments = Department::where('is_active', true)->get();

        return view('team.edit', compact('group', 'users', 'departments'));
    }

    /**
     * Store a new team group.
     */
    public function storeGroup(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'leader_id' => 'nullable|exists:users,id',
            'department_id' => 'nullable|exists:departments,id',
            'type' => 'required|in:research,project,committee,unit',
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'exists:users,id',
        ]);

        $validated['slug'] = \Str::slug($validated['name']);

        $group = TeamGroup::create($validated);

        // Add leader as member
        if (!empty($validated['leader_id'])) {
            $group->members()->attach($validated['leader_id'], [
                'role' => 'leader',
                'joined_at' => now(),
            ]);
        }

        // Add selected members
        if (!empty($validated['member_ids'])) {
            foreach ($validated['member_ids'] as $memberId) {
                // Don't add leader twice
                if ($memberId != $validated['leader_id']) {
                    $group->members()->attach($memberId, [
                        'role' => 'member',
                        'joined_at' => now(),
                    ]);
                }
            }
        }

        return redirect()->route('team.groups')
            ->with('success', __('Team group created successfully.'));
    }

    /**
     * Update a team group.
     */
    public function updateGroup(Request $request, TeamGroup $group)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'leader_id' => 'nullable|exists:users,id',
            'department_id' => 'nullable|exists:departments,id',
            'type' => 'required|in:research,project,committee,unit',
            'is_active' => 'boolean',
            'member_ids' => 'nullable|array',
            'member_ids.*' => 'exists:users,id',
        ]);

        $group->update($validated);

        // Update members
        $memberIds = $validated['member_ids'] ?? [];
        
        // Make sure leader is in the members list
        if (!empty($validated['leader_id']) && !in_array($validated['leader_id'], $memberIds)) {
            $memberIds[] = $validated['leader_id'];
        }

        // Sync members with roles
        $syncData = [];
        foreach ($memberIds as $memberId) {
            $role = ($memberId == $validated['leader_id']) ? 'leader' : 'member';
            $syncData[$memberId] = [
                'role' => $role,
                'joined_at' => $group->members()->where('user_id', $memberId)->first()?->pivot->joined_at ?? now(),
            ];
        }
        $group->members()->sync($syncData);

        return redirect()->route('team.groups')
            ->with('success', __('Team group updated successfully.'));
    }

    /**
     * Delete a team group.
     */
    public function destroyGroup(TeamGroup $group)
    {
        $group->members()->detach();
        $group->delete();

        return redirect()->route('team.groups')
            ->with('success', __('Team group deleted successfully.'));
    }

    /**
     * Add member to group.
     */
    public function addMember(Request $request, TeamGroup $group)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:leader,member',
        ]);

        if ($group->members()->where('user_id', $validated['user_id'])->exists()) {
            return redirect()->route('team.groups')
                ->with('error', __('User is already a member of this group.'));
        }

        $group->members()->attach($validated['user_id'], [
            'role' => $validated['role'],
            'joined_at' => now(),
        ]);

        return redirect()->route('team.groups')
            ->with('success', __('Member added successfully.'));
    }

    /**
     * Remove member from group.
     */
    public function removeMember(TeamGroup $group, User $user)
    {
        $group->members()->detach($user->id);

        return redirect()->route('team.groups')
            ->with('success', __('Member removed successfully.'));
    }
}
