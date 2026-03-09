<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'evaluator_id',
        'period',
        'year',
        'teaching_score',
        'research_score',
        'service_score',
        'collaboration_score',
        'communication_score',
        'initiative_score',
        'overall_score',
        'strengths',
        'areas_for_improvement',
        'goals',
        'evaluator_comments',
        'status',
        'submitted_at',
        'acknowledged_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'year' => 'integer',
        'teaching_score' => 'integer',
        'research_score' => 'integer',
        'service_score' => 'integer',
        'collaboration_score' => 'integer',
        'communication_score' => 'integer',
        'initiative_score' => 'integer',
        'overall_score' => 'decimal:2',
        'submitted_at' => 'datetime',
        'acknowledged_at' => 'datetime',
    ];

    /**
     * Get the user being evaluated.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the evaluator.
     */
    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    /**
     * Calculate the overall score.
     */
    public function calculateOverallScore(): float
    {
        $scores = [
            $this->teaching_score,
            $this->research_score,
            $this->service_score,
            $this->collaboration_score,
            $this->communication_score,
            $this->initiative_score,
        ];

        return round(array_sum($scores) / count($scores), 2);
    }

    /**
     * Get the scores as an array for radar chart.
     */
    public function getRadarData(): array
    {
        return [
            'labels' => ['Teaching', 'Research', 'Service', 'Collaboration', 'Communication', 'Initiative'],
            'data' => [
                $this->teaching_score,
                $this->research_score,
                $this->service_score,
                $this->collaboration_score,
                $this->communication_score,
                $this->initiative_score,
            ],
        ];
    }

    /**
     * Check if evaluation is draft.
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Check if evaluation is submitted.
     */
    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    /**
     * Get the status badge class.
     */
    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'draft' => 'badge-secondary',
            'submitted' => 'badge-warning',
            'reviewed' => 'badge-info',
            'acknowledged' => 'badge-success',
            default => 'badge-secondary',
        };
    }
}
