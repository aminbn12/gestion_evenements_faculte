<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'organization',
        'location',
        'start_date',
        'end_date',
        'is_current',
        'description',
        'achievements',
        'type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
        'achievements' => 'array',
    ];

    /**
     * Get the user that owns the experience.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the duration in years.
     */
    public function getDurationInYears(): float
    {
        $endDate = $this->is_current ? now() : $this->end_date;
        return $this->start_date->diffInYears($endDate);
    }

    /**
     * Get the duration string.
     */
    public function getDurationStringAttribute(): string
    {
        $endDate = $this->is_current ? now() : $this->end_date;
        $years = $this->start_date->diffInYears($endDate);
        $months = $this->start_date->diffInMonths($endDate) % 12;

        $parts = [];
        if ($years > 0) {
            $parts[] = $years . ' ' . str('year')->plural($years);
        }
        if ($months > 0) {
            $parts[] = $months . ' ' . str('month')->plural($months);
        }

        return implode(', ', $parts) ?: 'Less than a month';
    }
}
