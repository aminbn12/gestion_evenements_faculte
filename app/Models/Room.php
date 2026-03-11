<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'prof_capacity',
        'resident_capacity',
    ];

    protected $casts = [
        'prof_capacity' => 'integer',
        'resident_capacity' => 'integer',
    ];

    /**
     * Get the assignments for this room.
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(ExamAssignment::class);
    }

    /**
     * Get total capacity.
     */
    public function getTotalCapacityAttribute(): int
    {
        return $this->prof_capacity + $this->resident_capacity;
    }
}
