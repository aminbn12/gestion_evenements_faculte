<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'time',
        'duration',
        'promo',
        'subject',
    ];

    protected $casts = [
        'date' => 'date',
        'duration' => 'integer',
    ];

    /**
     * Get the assignments for this exam.
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(ExamAssignment::class);
    }

    /**
     * Get formatted date and time.
     */
    public function getFormattedDateTimeAttribute(): string
    {
        return "{$this->date} à {$this->time}";
    }
}
