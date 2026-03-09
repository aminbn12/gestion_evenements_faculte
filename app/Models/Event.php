<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'slug',
        'description',
        'department_id',
        'created_by',
        'start_date',
        'end_date',
        'is_all_day',
        'location',
        'address',
        'city',
        'type',
        'priority',
        'status',
        'capacity',
        'registered_count',
        'is_public',
        'requires_registration',
        'featured_image',
        'attachments',
        'reminder_days_before',
        'auto_reminder_enabled',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_all_day' => 'boolean',
        'is_public' => 'boolean',
        'requires_registration' => 'boolean',
        'auto_reminder_enabled' => 'boolean',
        'attachments' => 'array',
    ];

    /**
     * Get the department that owns the event.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the user who created the event.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the users assigned to the event.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'event_assignments')
            ->withPivot('role', 'status', 'notes')
            ->withTimestamps();
    }

    /**
     * Get the assignments for the event.
     */
    public function assignments()
    {
        return $this->hasMany(EventAssignment::class);
    }

    /**
     * Get the alerts for the event.
     */
    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }

    /**
     * Get the organizers of the event.
     */
    public function organizers()
    {
        return $this->users()->wherePivot('role', 'organizer');
    }

    /**
     * Get the presenters of the event.
     */
    public function presenters()
    {
        return $this->users()->wherePivot('role', 'presenter');
    }

    /**
     * Get the participants of the event.
     */
    public function participants()
    {
        return $this->users()->wherePivot('role', 'participant');
    }

    /**
     * Check if event is upcoming.
     */
    public function isUpcoming(): bool
    {
        return $this->start_date->isFuture();
    }

    /**
     * Check if event is ongoing.
     */
    public function isOngoing(): bool
    {
        return $this->start_date->isPast() && $this->end_date->isFuture();
    }

    /**
     * Check if event is past.
     */
    public function isPast(): bool
    {
        return $this->end_date->isPast();
    }

    /**
     * Get the duration in hours.
     */
    public function getDurationInHours(): float
    {
        return $this->start_date->diffInHours($this->end_date);
    }

    /**
     * Get the priority badge class.
     */
    public function getPriorityBadgeClass(): string
    {
        return match ($this->priority) {
            'low' => 'badge-success',
            'medium' => 'badge-warning',
            'high' => 'badge-danger',
            'critical' => 'badge-dark',
            default => 'badge-secondary',
        };
    }

    /**
     * Get the status badge class.
     */
    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'draft' => 'badge-secondary',
            'published' => 'badge-primary',
            'cancelled' => 'badge-danger',
            'completed' => 'badge-success',
            default => 'badge-secondary',
        };
    }
}
