<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Skill;
use App\Models\Experience;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function show()
    {
        $user = Auth::user();
        $user->load(['profile', 'academicInfo', 'skills', 'experiences', 'documents', 'leaves', 'evaluations']);

        return view('profile.show', compact('user'));
    }

    /**
     * Show the form for editing the user's profile.
     */
    public function edit()
    {
        $user = Auth::user();
        $user->load(['profile', 'academicInfo', 'skills']);

        return view('profile.edit', compact('user'));
    }

    /**
     * Update the user's profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            // User fields
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',

            // Profile fields
            'profile.birth_date' => 'nullable|date',
            'profile.gender' => 'nullable|in:male,female',
            'profile.nationality' => 'nullable|string|max:255',
            'profile.cin' => 'nullable|string|max:255',
            'profile.address' => 'nullable|string|max:255',
            'profile.city' => 'nullable|string|max:255',
            'profile.country' => 'nullable|string|max:255',
            'profile.postal_code' => 'nullable|string|max:20',
            'profile.emergency_contact_name' => 'nullable|string|max:255',
            'profile.emergency_contact_phone' => 'nullable|string|max:20',
            'profile.emergency_contact_relation' => 'nullable|string|max:255',
            'profile.linkedin' => 'nullable|string|max:255',
            'profile.orcid' => 'nullable|string|max:255',
            'profile.researchgate' => 'nullable|string|max:255',
            'profile.google_scholar' => 'nullable|string|max:255',
            'profile.bio' => 'nullable|string',

            // Academic info fields
            'academic_info.grade' => 'nullable|in:assistant,maitre_assistant,maitre_conference,professeur,doctorant,vacataire',
            'academic_info.specialty' => 'nullable|string|max:255',
            'academic_info.research_domain' => 'nullable|string|max:255',
            'academic_info.recruitment_date' => 'nullable|date',
            'academic_info.contract_type' => 'nullable|in:permanent,contract,vacataire,visiting',
            'academic_info.office_location' => 'nullable|string|max:255',
            'academic_info.office_phone' => 'nullable|string|max:20',
            'academic_info.highest_degree' => 'nullable|string|max:255',
            'academic_info.degree_institution' => 'nullable|string|max:255',
            'academic_info.degree_year' => 'nullable|integer|min:1950|max:' . date('Y'),
            'academic_info.teaching_hours_per_week' => 'nullable|integer|min:0|max:40',
        ]);

        // Update user
        $user->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'phone' => $validated['phone'] ?? null,
        ]);

        // Update or create profile
        if (isset($validated['profile'])) {
            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                $validated['profile']
            );
        }

        // Update or create academic info
        if (isset($validated['academic_info'])) {
            $user->academicInfo()->updateOrCreate(
                ['user_id' => $user->id],
                $validated['academic_info']
            );
        }

        return redirect()->route('profile.show')
            ->with('success', __('Profile updated successfully.'));
    }

    /**
     * Upload avatar.
     */
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        // Delete old avatar
        if ($user->avatar) {
            Storage::disk('public')->delete('avatars/' . $user->avatar);
        }

        // Store new avatar
        $avatarName = $user->id . '_' . time() . '.' . $request->avatar->extension();
        $request->avatar->storeAs('avatars', $avatarName, 'public');

        $user->update(['avatar' => $avatarName]);

        return redirect()->route('profile.show')
            ->with('success', __('Avatar updated successfully.'));
    }

    /**
     * Add skill to user.
     */
    public function addSkill(Request $request)
    {
        $validated = $request->validate([
            'skill_id' => 'required|exists:skills,id',
            'level' => 'required|in:beginner,intermediate,advanced,expert',
            'years_of_experience' => 'nullable|integer|min:0|max:50',
            'notes' => 'nullable|string',
        ]);

        $user = Auth::user();

        // Check if skill already exists
        if ($user->skills()->where('skill_id', $validated['skill_id'])->exists()) {
            return redirect()->route('profile.show')
                ->with('error', __('You already have this skill.'));
        }

        $user->skills()->attach($validated['skill_id'], [
            'level' => $validated['level'],
            'years_of_experience' => $validated['years_of_experience'] ?? 0,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('profile.show')
            ->with('success', __('Skill added successfully.'));
    }

    /**
     * Remove skill from user.
     */
    public function removeSkill(Skill $skill)
    {
        Auth::user()->skills()->detach($skill->id);

        return redirect()->route('profile.show')
            ->with('success', __('Skill removed successfully.'));
    }

    /**
     * Add experience to user.
     */
    public function addExperience(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'organization' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date|required_if:is_current,0',
            'is_current' => 'boolean',
            'description' => 'nullable|string',
            'type' => 'required|in:academic,professional,research,administrative',
        ]);

        Auth::user()->experiences()->create($validated);

        return redirect()->route('profile.show')
            ->with('success', __('Experience added successfully.'));
    }

    /**
     * Remove experience from user.
     */
    public function removeExperience(Experience $experience)
    {
        if ($experience->user_id !== Auth::id()) {
            abort(403);
        }

        $experience->delete();

        return redirect()->route('profile.show')
            ->with('success', __('Experience removed successfully.'));
    }

    /**
     * Upload document.
     */
    public function uploadDocument(Request $request)
    {
        $validated = $request->validate([
            'document' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'name' => 'required|string|max:255',
            'type' => 'required|in:contract,diploma,certificate,cv,publication,other',
            'description' => 'nullable|string',
            'expiry_date' => 'nullable|date',
            'is_confidential' => 'boolean',
        ]);

        $user = Auth::user();

        $file = $request->file('document');
        $path = $file->store('documents/' . $user->id, 'public');

        Document::create([
            'user_id' => $user->id,
            'uploaded_by' => $user->id,
            'name' => $validated['name'],
            'original_name' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'expiry_date' => $validated['expiry_date'] ?? null,
            'is_confidential' => $validated['is_confidential'] ?? false,
        ]);

        return redirect()->route('profile.show')
            ->with('success', __('Document uploaded successfully.'));
    }

    /**
     * Download document.
     */
    public function downloadDocument(Document $document)
    {
        if ($document->user_id !== Auth::id() && !Auth::user()->hasPermission('view-all-profiles')) {
            abort(403);
        }

        return Storage::disk('public')->download($document->path, $document->original_name);
    }

    /**
     * Delete document.
     */
    public function deleteDocument(Document $document)
    {
        if ($document->user_id !== Auth::id()) {
            abort(403);
        }

        Storage::disk('public')->delete($document->path);
        $document->delete();

        return redirect()->route('profile.show')
            ->with('success', __('Document deleted successfully.'));
    }
}
