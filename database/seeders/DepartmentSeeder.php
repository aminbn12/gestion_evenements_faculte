<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Département planification',
                'slug' => 'planification',
                'description' => 'Département de la planification et de l\'organisation',
                'is_active' => true,
            ],
            [
                'name' => 'Département scolarité',
                'slug' => 'scolarite',
                'description' => 'Département de la scolarité et des affaires étudiantes',
                'is_active' => true,
            ],
            [
                'name' => 'Département des moyens généraux',
                'slug' => 'moyens-generaux',
                'description' => 'Département des moyens généraux et des ressources',
                'is_active' => true,
            ],
            [
                'name' => 'Département informatique',
                'slug' => 'informatique',
                'description' => 'Département informatique et des systèmes d\'information',
                'is_active' => true,
            ],
            [
                'name' => 'Département reprographie',
                'slug' => 'reprographie',
                'description' => 'Département de la reprographie et de l\'impression',
                'is_active' => true,
            ],
            [
                'name' => 'Département des préparateurs',
                'slug' => 'preparateurs',
                'description' => 'Département des préparateurs et assistants pédagogiques',
                'is_active' => true,
            ],
            [
                'name' => 'Département de la bibliothèque',
                'slug' => 'bibliotheque',
                'description' => 'Département de la bibliothèque et de la documentation',
                'is_active' => true,
            ],
            [
                'name' => 'Département résidanat',
                'slug' => 'residanat',
                'description' => 'Département de la résidanat et du logement étudiant',
                'is_active' => true,
            ],
            [
                'name' => 'Département des enseignants',
                'slug' => 'enseignants',
                'description' => 'Département des enseignants et du corps professoral',
                'is_active' => true,
            ],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
