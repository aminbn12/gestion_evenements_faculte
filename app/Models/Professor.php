<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Professor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'rank',
        'responsible_promo',
        'subject',
    ];

    /**
     * Get the users associated with this professor.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the assignments for this professor.
     */
    public function assignments(): BelongsToMany
    {
        return $this->belongsToMany(ExamAssignment::class, 'professor_assignments');
    }

    /**
     * Get absences for this professor.
     */
    public function absences(): HasMany
    {
        return $this->hasMany(Absence::class);
    }
}
