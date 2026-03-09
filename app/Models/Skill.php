<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'category',
        'description',
    ];

    /**
     * Get the users that have this skill.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_skills')
            ->withPivot('level', 'years_of_experience', 'notes')
            ->withTimestamps();
    }
}
