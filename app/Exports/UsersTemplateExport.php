<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersTemplateExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Sample data with all profile types
        return collect([
            [
                'Prenom' => 'Ahmed',
                'Nom' => 'Alami',
                'Email' => 'alami@email.com',
                'Telephone' => '0612345678',
                'Role' => 'Enseignant',
                'Departement' => 'Médecine Dentaire',
                'Grade' => 'Pr',
                'Responsable_Promo' => 'FM6MD 1',
                'Matiere' => 'Anatomie Dentaire',
                'Statut' => 'active',
            ],
            [
                'Prenom' => 'Sarah',
                'Nom' => 'Berrada',
                'Email' => 'berrada@email.com',
                'Telephone' => '0698765432',
                'Role' => 'Résidanat',
                'Departement' => 'Médecine Dentaire',
                'Grade' => '',
                'Responsable_Promo' => '',
                'Matiere' => '',
                'Niveau' => 'A1',
                'Specialite' => 'Odontologie Conservatrice',
                'Statut' => 'active',
            ],
            [
                'Prenom' => 'Mohamed',
                'Nom' => 'Khali',
                'Email' => 'khali@email.com',
                'Telephone' => '0612345679',
                'Role' => 'Manager',
                'Departement' => 'Administration',
                'Grade' => '',
                'Responsable_Promo' => '',
                'Matiere' => '',
                'Statut' => 'active',
            ],
        ]);
    }

    /**
    * @return array
    */
    public function headings(): array
    {
        return [
            'Prenom',
            'Nom',
            'Email',
            'Telephone',
            'Role',
            'Departement',
            'Grade', // Pour Enseignant: Pr, Dr
            'Responsable_Promo', // Pour Enseignant: LTLP 1-3, FM6MD 1-5
            'Matiere', // Pour Enseignant
            'Niveau', // Pour Résidanat: A1, A2, A3, A4
            'Specialite', // Pour Résidanat
            'Statut',
        ];
    }
}
