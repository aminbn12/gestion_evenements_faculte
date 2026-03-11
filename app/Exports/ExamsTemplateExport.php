<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExamsTemplateExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Sample data with new promo names
        return collect([
            [
                'Date' => '2024-06-15',
                'Heure' => '09:00',
                'Duree_minutes' => '120',
                'Promo' => 'FM6MD 1',
                'Matiere' => 'Anatomie Dentaire',
            ],
            [
                'Date' => '2024-06-15',
                'Heure' => '14:00',
                'Duree_minutes' => '90',
                'Promo' => 'FM6MD 2',
                'Matiere' => 'Physiologie',
            ],
            [
                'Date' => '2024-06-16',
                'Heure' => '09:00',
                'Duree_minutes' => '60',
                'Promo' => 'LTLP 1',
                'Matiere' => 'Biochimie',
            ],
            [
                'Date' => '2024-06-16',
                'Heure' => '14:00',
                'Duree_minutes' => '120',
                'Promo' => 'LTLP 2',
                'Matiere' => 'Immunologie',
            ],
            [
                'Date' => '2024-06-17',
                'Heure' => '09:00',
                'Duree_minutes' => '90',
                'Promo' => 'FM6MD 3',
                'Matiere' => 'Pharmacologie',
            ],
        ]);
    }

    /**
    * @return array
    */
    public function headings(): array
    {
        return [
            'Date',
            'Heure',
            'Duree_minutes',
            'Promo',
            'Matiere',
        ];
    }
}
