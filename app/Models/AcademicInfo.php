<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicInfo extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'grade',
        'specialty',
        'research_domain',
        'recruitment_date',
        'contract_type',
        'office_location',
        'office_phone',
        'highest_degree',
        'degree_institution',
        'degree_year',
        'teaching_hours_per_week',
        'courses',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'recruitment_date' => 'date',
        'degree_year' => 'integer',
        'teaching_hours_per_week' => 'integer',
        'courses' => 'array',
    ];

    /**
     * Get the user that owns the academic info.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the grade label.
     */
    public function getGradeLabelAttribute(): string
    {
        return match ($this->grade) {
            'assistant' => 'Assistant',
            'maitre_assistant' => 'Maître Assistant',
            'maitre_conference' => 'Maître de Conférence',
            'professeur' => 'Professeur',
            'doctorant' => 'Doctorant',
            'vacataire' => 'Vacataire',
            default => $this->grade,
        };
    }
}
