<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resident extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'level',
        'specialty',
    ];

    protected $casts = [
        'level' => 'integer',
    ];

    /**
     * Get the user associated with this resident.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the assignments for this resident.
     */
    public function assignments(): BelongsToMany
    {
        return $this->belongsToMany(ExamAssignment::class, 'resident_assignments');
    }

    /**
     * Get absences for this resident.
     */
    public function absences(): HasMany
    {
        return $this->hasMany(Absence::class);
    }

    /**
     * Get formatted level.
     */
    public function getFormattedLevelAttribute(): string
    {
        return "A{$this->level}";
    }
}
