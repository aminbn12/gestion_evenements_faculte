<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Professor;
use App\Models\Resident;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return User::with(['role', 'department'])->get();
    }

    /**
    * @return array
     */
    public function headings(): array
    {
        return [
            'Prénom',
            'Nom',
            'Email',
            'Rôle',
            'Département',
            'Téléphone',
            'Grade',
            'Responsable Promo',
            'Matière',
            'Niveau',
            'Spécialité',
            'Statut',
        ];
    }

    /**
    * @param mixed $user
    * @return array
     */
    public function map($user): array
    {
        // Get professor or resident details
        $professor = Professor::where('user_id', $user->id)->first();
        $resident = Resident::where('user_id', $user->id)->first();
        
        return [
            $user->first_name,
            $user->last_name,
            $user->email,
            $user->role->name ?? 'N/A',
            $user->department->name ?? 'N/A',
            $user->phone ?? '',
            $professor->rank ?? '',
            $professor->responsible_promo ?? '',
            $professor->subject ?? '',
            $resident ? 'A' . $resident->level : '',
            $resident->specialty ?? '',
            $user->status,
        ];
    }
}
