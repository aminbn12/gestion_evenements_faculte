<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use App\Models\Professor;
use App\Models\Resident;
use App\Exports\UsersExport;
use App\Exports\UsersTemplateExport;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $query = User::with(['role', 'department']);

        // Filter by role
        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        // Filter by department
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                    ->orWhere('last_name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);
        $roles = Role::all();
        $departments = Department::where('is_active', true)->get();

        return view('users.index', compact('users', 'roles', 'departments'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = Role::all();
        $departments = Department::where('is_active', true)->get();

        return view('users.create', compact('roles', 'departments'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'nullable|exists:roles,id',
            'department_id' => 'nullable|exists:departments,id',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive,pending',
            // Professor fields
            'rank' => 'nullable|string|max:50',
            'responsible_promo' => 'nullable|string|max:50',
            'subject' => 'nullable|string|max:255',
            // Resident fields
            'level' => 'nullable|integer|min:1|max:4',
            'specialty' => 'nullable|string|max:255',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        // Create profile
        $user->profile()->create([]);

        // Create Professor or Resident record if applicable
        $role = Role::find($validated['role_id']);
        if ($role) {
            if ($role->slug === 'enseignant') {
                // Create professor record
                Professor::create([
                    'user_id' => $user->id,
                    'name' => $user->full_name,
                    'rank' => $request->rank ?? 'Pr',
                    'responsible_promo' => $request->responsible_promo,
                    'subject' => $request->subject,
                ]);
            } elseif ($role->slug === 'residanat') {
                // Create resident record
                Resident::create([
                    'user_id' => $user->id,
                    'name' => $user->full_name,
                    'level' => $request->level ?? 1,
                    'specialty' => $request->specialty,
                ]);
            }
        }

        return redirect()->route('users.index')
            ->with('success', __('User created successfully.'));
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load(['role', 'department', 'profile', 'academicInfo', 'skills', 'experiences']);

        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $departments = Department::where('is_active', true)->get();

        // Get associated professor or resident record
        $professor = Professor::where('user_id', $user->id)->first();
        $resident = Resident::where('user_id', $user->id)->first();

        return view('users.edit', compact('user', 'roles', 'departments', 'professor', 'resident'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'nullable|exists:roles,id',
            'department_id' => 'nullable|exists:departments,id',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive,pending',
            // Professor fields
            'rank' => 'nullable|string|max:50',
            'responsible_promo' => 'nullable|string|max:50',
            'subject' => 'nullable|string|max:255',
            // Resident fields
            'level' => 'nullable|integer|min:1|max:4',
            'specialty' => 'nullable|string|max:255',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        // Update or create Professor record
        $role = Role::find($validated['role_id']);
        if ($role && $role->slug === 'enseignant') {
            Professor::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'name' => $user->full_name,
                    'rank' => $request->rank ?? 'Pr',
                    'responsible_promo' => $request->responsible_promo,
                    'subject' => $request->subject,
                ]
            );
        } else {
            // Remove professor record if role changed
            Professor::where('user_id', $user->id)->delete();
        }

        // Update or create Resident record
        if ($role && $role->slug === 'residanat') {
            Resident::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'name' => $user->full_name,
                    'level' => $request->level ?? 1,
                    'specialty' => $request->specialty,
                ]
            );
        } else {
            // Remove resident record if role changed
            Resident::where('user_id', $user->id)->delete();
        }

        return redirect()->route('users.index')
            ->with('success', __('User updated successfully.'));
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        // Prevent deleting self
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')
                ->with('error', __('You cannot delete your own account.'));
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', __('User deleted successfully.'));
    }

    /**
     * Toggle user status.
     */
    public function toggleStatus(User $user)
    {
        // Prevent toggling self
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')
                ->with('error', __('You cannot change your own status.'));
        }

        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();

        return redirect()->route('users.index')
            ->with('success', __('User status updated successfully.'));
    }

    /**
     * Export users to Excel.
     */
    public function exportUsers()
    {
        return Excel::download(new UsersExport, 'utilisateurs_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Import users from Excel.
     */
    public function importUsers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,xls,csv'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            Excel::import(new UsersImport, $request->file('file'));

            return redirect()->route('users.index')
                ->with('success', 'Utilisateurs importés avec succès!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de l\'importation: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Download user template.
     */
    public function downloadUserTemplate()
    {
        return Excel::download(new UsersTemplateExport, 'modele_utilisateurs.xlsx');
    }
}
