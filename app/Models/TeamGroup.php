<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamGroup extends Model
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
        'description',
        'leader_id',
        'department_id',
        'type',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the leader of the team group.
     */
    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    /**
     * Get the department that owns the team group.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the members of the team group.
     */
    public function members()
    {
        return $this->belongsToMany(User::class, 'team_group_members')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    /**
     * Get the type label.
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'research' => 'Équipe de Recherche',
            'project' => 'Équipe Projet',
            'committee' => 'Comité',
            'unit' => 'Unité',
            default => $this->type,
        };
    }

    /**
     * Get the member count.
     */
    public function getMemberCountAttribute(): int
    {
        return $this->members()->count();
    }
}
