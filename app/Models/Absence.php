<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absence extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'professor_id',
        'resident_id',
        'reason',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Get the professor for this absence.
     */
    public function professor(): BelongsTo
    {
        return $this->belongsTo(Professor::class);
    }

    /**
     * Get the resident for this absence.
     */
    public function resident(): BelongsTo
    {
        return $this->belongsTo(Resident::class);
    }

    /**
     * Get the person name (professor or resident).
     */
    public function getPersonNameAttribute(): string
    {
        if ($this->professor) {
            return $this->professor->name;
        }
        if ($this->resident) {
            return $this->resident->name;
        }
        return 'Inconnu';
    }
}
