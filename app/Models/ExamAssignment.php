<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ExamAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'room_id',
    ];

    /**
     * Get the exam for this assignment.
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Get the room for this assignment.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the professors for this assignment.
     */
    public function professors(): BelongsToMany
    {
        return $this->belongsToMany(Professor::class, 'professor_assignments');
    }

    /**
     * Get the residents for this assignment.
     */
    public function residents(): BelongsToMany
    {
        return $this->belongsToMany(Resident::class, 'resident_assignments');
    }
}
