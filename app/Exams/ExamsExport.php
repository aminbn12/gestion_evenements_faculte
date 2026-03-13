<?php

namespace App\Exports;

use App\Models\Exam;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExamsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Exam::with(['assignments.room', 'assignments.professors', 'assignments.residents'])->get();
    }

    /**
    * @return array
     */
    public function headings(): array
    {
        return [
            'Date',
            'Heure',
            'Durée (minutes)',
            'Promo',
            'Matière',
            'Salle(s)',
            'Enseignants (Surveillants)',
            'Résidents (Surveillants)',
        ];
    }

    /**
    * @param mixed $exam
    * @return array
     */
    public function map($exam): array
    {
        // Get rooms for this exam
        $rooms = $exam->assignments->map(function ($assignment) {
            return $assignment->room->name ?? '';
        })->filter()->implode(', ');

        // Get professors for this exam
        $professors = $exam->assignments->flatMap(function ($assignment) {
            return $assignment->professors;
        })->map(function ($prof) {
            return $prof->name ?? '';
        })->filter()->unique()->implode(', ');

        // Get residents for this exam
        $residents = $exam->assignments->flatMap(function ($assignment) {
            return $assignment->residents;
        })->map(function ($resident) {
            return $resident->name ?? '';
        })->filter()->unique()->implode(', ');

        return [
            $exam->date,
            $exam->time,
            $exam->duration,
            $exam->promo,
            $exam->subject,
            $rooms,
            $professors,
            $residents,
        ];
    }
}
