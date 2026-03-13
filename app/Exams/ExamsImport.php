<?php

namespace App\Exams;

use App\Models\Exam;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Str;

class ExamsImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Check if exam already exists (by date, time, and subject)
        $existingExam = Exam::where('date', $row['date'])
            ->where('time', $row['time'])
            ->where('subject', $row['matiere'])
            ->first();

        if ($existingExam) {
            // Update existing exam
            $existingExam->update([
                'duration' => $row['duree_minutes'] ?? 60,
                'promo' => $row['promo'] ?? 'FM6MD1',
            ]);
            return null;
        }

        return new Exam([
            'date' => $row['date'],
            'time' => $row['heure'],
            'duration' => $row['duree_minutes'] ?? 60,
            'promo' => $row['promo'] ?? 'FM6MD1',
            'subject' => $row['matiere'],
        ]);
    }

    /**
    * @return array
     */
    public function rules(): array
    {
        return [
            'date' => 'required|date',
            'heure' => 'required',
            'duree_minutes' => 'nullable|integer|min:1',
            'promo' => 'required|string',
            'matiere' => 'required|string|max:200',
        ];
    }

    /**
    * @return array
     */
    public function customValidationAttributes()
    {
        return [
            'date' => 'Date',
            'heure' => 'Heure',
            'duree_minutes' => 'Durée',
            'promo' => 'Promotion',
            'matiere' => 'Matière',
        ];
    }
}
