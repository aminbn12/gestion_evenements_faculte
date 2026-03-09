<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use App\Models\Profile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = Role::pluck('id', 'slug');
        $departments = Department::pluck('id', 'slug');

        $users = [
            [
                'first_name' => 'Admin',
                'last_name' => 'Manager',
                'email' => 'manager@faculte.ma',
                'password' => 'password',
                'role_id' => $roles['manager'],
                'department_id' => null,
                'status' => 'active',
            ],
            [
                'first_name' => 'Ahmed',
                'last_name' => 'Bennani',
                'email' => 'chef.dept@faculte.ma',
                'password' => 'password',
                'role_id' => $roles['chef-dept'],
                'department_id' => $departments['informatique'],
                'status' => 'active',
            ],
            [
                'first_name' => 'Fatima',
                'last_name' => 'Zahra',
                'email' => 'enseignant@faculte.ma',
                'password' => 'password',
                'role_id' => $roles['enseignant'],
                'department_id' => $departments['informatique'],
                'status' => 'active',
            ],
            [
                'first_name' => 'Mohammed',
                'last_name' => 'Alami',
                'email' => 'technicien@faculte.ma',
                'password' => 'password',
                'role_id' => $roles['technicien'],
                'department_id' => $departments['informatique'],
                'status' => 'active',
            ],
            [
                'first_name' => 'Sara',
                'last_name' => 'Idrissi',
                'email' => 'secretaire@faculte.ma',
                'password' => 'password',
                'role_id' => $roles['secretaire'],
                'department_id' => null,
                'status' => 'active',
            ],
            // Additional users for each department
            [
                'first_name' => 'Youssef',
                'last_name' => 'Amrani',
                'email' => 'chef.scolarite@faculte.ma',
                'password' => 'password',
                'role_id' => $roles['chef-dept'],
                'department_id' => $departments['scolarite'],
                'status' => 'active',
            ],
            [
                'first_name' => 'Nadia',
                'last_name' => 'El Idrissi',
                'email' => 'nadia.scolarite@faculte.ma',
                'password' => 'password',
                'role_id' => $roles['secretaire'],
                'department_id' => $departments['scolarite'],
                'status' => 'active',
            ],
            [
                'first_name' => 'Omar',
                'last_name' => 'Berrada',
                'email' => 'chef.moyens@faculte.ma',
                'password' => 'password',
                'role_id' => $roles['chef-dept'],
                'department_id' => $departments['moyens-generaux'],
                'status' => 'active',
            ],
            [
                'first_name' => 'Hicham',
                'last_name' => 'Kasmi',
                'email' => 'hicham.moyens@faculte.ma',
                'password' => 'password',
                'role_id' => $roles['technicien'],
                'department_id' => $departments['moyens-generaux'],
                'status' => 'active',
            ],
            [
                'first_name' => 'Karim',
                'last_name' => 'Fadili',
                'email' => 'chef.info@faculte.ma',
                'password' => 'password',
                'role_id' => $roles['chef-dept'],
                'department_id' => $departments['informatique'],
                'status' => 'active',
            ],
            [
                'first_name' => 'Ali',
                'last_name' => 'Boukhalef',
                'email' => 'ali.info@faculte.ma',
                'password' => 'password',
                'role_id' => $roles['technicien'],
                'department_id' => $departments['informatique'],
                'status' => 'active',
            ],
            [
                'first_name' => 'Rachid',
                'last_name' => 'Mrani',
                'email' => 'rachid.repro@faculte.ma',
                'password' => 'password',
                'role_id' => $roles['chef-dept'],
                'department_id' => $departments['reprographie'],
                'status' => 'active',
            ],
            [
                'first_name' => 'Mouna',
                'last_name' => 'El Mourabit',
                'email' => 'mouna.repro@faculte.ma',
                'password' => 'password',
                'role_id' => $roles['technicien'],
                'department_id' => $departments['reprographie'],
                'status' => 'active',
            ],
            [
                'first_name' => 'Abdelkader',
                'last_name' => 'El Ghanmi',
                'email' => 'chef.preparateurs@faculte.ma',
                'password' => 'password',
                'role_id' => $roles['chef-dept'],
                'department_id' => $departments['preparateurs'],
                'status' => 'active',
            ],
            [
                'first_name' => 'Imane',
                'last_name' => 'Ben Hamza',
                'email' => 'imane.prep@faculte.ma',
                'password' => 'password',
                'role_id' => $roles['technicien'],
                'department_id' => $departments['preparateurs'],
                'status' => 'active',
            ],
            [
                'first_name' => 'Jamal',
                'last_name' => 'El Fassi',
                'email' => 'chef.biblio@faculte.ma',
                'password' => 'password',
                'role_id' => $roles['chef-dept'],
                'department_id' => $departments['bibliotheque'],
                'status' => 'active',
            ],
            [
                'first_name' => 'Asmaa',
                'last_name' => 'El Kihal',
                'email' => 'asmaa.biblio@faculte.ma',
                'password' => 'password',
                'role_id' => $roles['secretaire'],
                'department_id' => $departments['bibliotheque'],
                'status' => 'active',
            ],
            [
                'first_name' => 'Tarik',
                'last_name' => 'El Aissaoui',
                'email' => 'chef.residanat@faculte.ma',
                'password' => 'password',
                'role_id' => $roles['chef-dept'],
                'department_id' => $departments['residanat'],
                'status' => 'active',
            ],
            [
                'first_name' => 'Souad',
                'last_name' => 'El Yazidi',
                'email' => 'souad.residanat@faculte.ma',
                'password' => 'password',
                'role_id' => $roles['secretaire'],
                'department_id' => $departments['residanat'],
                'status' => 'active',
            ],
            [
                'first_name' => 'Pr.',
                'last_name' => 'Mohamed Alaoui',
                'email' => 'prof.alaoui@faculte.ma',
                'password' => 'password',
                'role_id' => $roles['chef-dept'],
                'department_id' => $departments['enseignants'],
                'status' => 'active',
            ],
            [
                'first_name' => 'Pr.',
                'last_name' => 'Fatima El Amrani',
                'email' => 'prof.amrani@faculte.ma',
                'password' => 'password',
                'role_id' => $roles['enseignant'],
                'department_id' => $departments['enseignants'],
                'status' => 'active',
            ],
            [
                'first_name' => 'Pr.',
                'last_name' => 'Abdelilah El Bouzidi',
                'email' => 'prof.bouzidi@faculte.ma',
                'password' => 'password',
                'role_id' => $roles['enseignant'],
                'department_id' => $departments['enseignants'],
                'status' => 'active',
            ],
            [
                'first_name' => 'Pr.',
                'last_name' => 'Hakima El Hilali',
                'email' => 'prof.hilali@faculte.ma',
                'password' => 'password',
                'role_id' => $roles['enseignant'],
                'department_id' => $departments['enseignants'],
                'status' => 'active',
            ],
            [
                'first_name' => ' Khalid',
                'last_name' => 'El Ouardi',
                'email' => 'chef.planification@faculte.ma',
                'password' => 'password',
                'role_id' => $roles['chef-dept'],
                'department_id' => $departments['planification'],
                'status' => 'active',
            ],
            [
                'first_name' => 'Zineb',
                'last_name' => 'El Mghari',
                'email' => 'zineb.planification@faculte.ma',
                'password' => 'password',
                'role_id' => $roles['secretaire'],
                'department_id' => $departments['planification'],
                'status' => 'active',
            ],
        ];

        foreach ($users as $userData) {
            // Password will be automatically hashed by the model's 'hashed' cast
            $user = User::create($userData);
            
            // Create profile for each user
            Profile::create([
                'user_id' => $user->id,
            ]);
        }

        // Update department managers
        Department::where('slug', 'informatique')->update(['manager_id' => 2]);
    }
}
