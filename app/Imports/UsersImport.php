<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use App\Models\Professor;
use App\Models\Resident;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Find role by name (case-insensitive)
        $role = Role::whereRaw('LOWER(name) = ?', [strtolower($row['role'])])->first();
        
        // Find department by name (case-insensitive)
        $department = Department::whereRaw('LOWER(name) = ?', [strtolower($row['departement'])])->first();

        // Check if user already exists by email
        $existingUser = User::where('email', $row['email'])->first();

        if ($existingUser) {
            // Update existing user
            $existingUser->update([
                'first_name' => $row['prenom'],
                'last_name' => $row['nom'],
                'role_id' => $role?->id,
                'department_id' => $department?->id,
                'phone' => $row['telephone'] ?? '',
                'status' => $row['statut'] ?? 'active',
            ]);
            
            // Update professor/resident profile
            $this->updateProfile($existingUser, $row, $role);
            
            return null;
        }

        $user = new User([
            'first_name' => $row['prenom'],
            'last_name' => $row['nom'],
            'email' => $row['email'],
            'password' => Hash::make('password123'), // Default password
            'role_id' => $role?->id,
            'department_id' => $department?->id,
            'phone' => $row['telephone'] ?? '',
            'status' => $row['statut'] ?? 'active',
        ]);
        
        // Create professor/resident profile
        $this->createProfile($user, $row, $role);
        
        return $user;
    }
    
    /**
     * Create professor or resident profile
     */
    private function createProfile($user, array $row, $role)
    {
        if (!$role) return;
        
        $slug = strtolower($role->slug);
        
        if ($slug === 'enseignant') {
            Professor::create([
                'user_id' => $user->id,
                'name' => $user->full_name,
                'rank' => $row['grade'] ?? 'Pr',
                'responsible_promo' => $row['responsable_promo'] ?? null,
                'subject' => $row['matiere'] ?? null,
            ]);
        } elseif ($slug === 'residanat') {
            Resident::create([
                'user_id' => $user->id,
                'name' => $user->full_name,
                'level' => $this->parseLevel($row['niveau'] ?? 'A1'),
                'specialty' => $row['specialite'] ?? null,
            ]);
        }
    }
    
    /**
     * Update professor or resident profile
     */
    private function updateProfile($user, array $row, $role)
    {
        if (!$role) return;
        
        $slug = strtolower($role->slug);
        
        if ($slug === 'enseignant') {
            Professor::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'name' => $user->full_name,
                    'rank' => $row['grade'] ?? 'Pr',
                    'responsible_promo' => $row['responsable_promo'] ?? null,
                    'subject' => $row['matiere'] ?? null,
                ]
            );
        } elseif ($slug === 'residanat') {
            Resident::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'name' => $user->full_name,
                    'level' => $this->parseLevel($row['niveau'] ?? 'A1'),
                    'specialty' => $row['specialite'] ?? null,
                ]
            );
        }
    }
    
    /**
     * Parse level from string to integer
     */
    private function parseLevel($level)
    {
        if (is_numeric($level)) {
            return (int) $level;
        }
        // Handle A1, A2, A3, A4 format
        if (preg_match('/A(\d+)/i', $level, $matches)) {
            return (int) $matches[1];
        }
        return 1;
    }

    /**
    * @return array
     */
    public function rules(): array
    {
        return [
            'prenom' => 'required|string|max:100',
            'nom' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'role' => 'nullable|string',
            'departement' => 'nullable|string',
            'telephone' => 'nullable|string|max:20',
            'statut' => 'nullable|string|in:active,inactive,pending',
            // Professor fields
            'grade' => 'nullable|string|max:50',
            'responsable_promo' => 'nullable|string|max:50',
            'matiere' => 'nullable|string|max:255',
            // Resident fields
            'niveau' => 'nullable|string|max:10',
            'specialite' => 'nullable|string|max:255',
        ];
    }

    /**
    * @return array
     */
    public function customValidationAttributes()
    {
        return [
            'prenom' => 'Prénom',
            'nom' => 'Nom',
            'email' => 'Email',
            'role' => 'Rôle',
            'departement' => 'Département',
            'telephone' => 'Téléphone',
            'statut' => 'Statut',
            'grade' => 'Grade',
            'responsable_promo' => 'Responsable Promo',
            'matiere' => 'Matière',
            'niveau' => 'Niveau',
            'specialite' => 'Spécialité',
        ];
    }
}
