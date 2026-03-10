<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'event_id',
        'created_by',
        'subject',
        'message',
        'send_email',
        'send_whatsapp',
        'recipient_type',
        'custom_recipients',
        'scheduled_at',
        'sent_at',
        'status',
        'error_message',
        'reminder_days',
        'department_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'send_email' => 'boolean',
        'send_whatsapp' => 'boolean',
        'custom_recipients' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'reminder_days' => 'integer',
    ];

    /**
     * Get the event that owns the alert.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the user who created the alert.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the department for department-based alerts.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the logs for the alert.
     */
    public function logs()
    {
        return $this->hasMany(AlertLog::class);
    }

    /**
     * Check if alert is scheduled.
     */
    public function isScheduled(): bool
    {
        return $this->status === 'scheduled' && $this->scheduled_at;
    }

    /**
     * Check if alert is sent.
     */
    public function isSent(): bool
    {
        return $this->status === 'sent';
    }

    /**
     * Get the recipients based on recipient type.
     */
    public function getRecipients()
    {
        return match ($this->recipient_type) {
            'all' => $this->event->users,
            'organizers' => $this->event->organizers,
            'presenters' => $this->event->presenters,
            'participants' => $this->event->participants,
            'role' => $this->getRoleRecipients(),
            'department' => $this->getDepartmentRecipients(),
            'users' => $this->getCustomUserRecipients(),
            'manager' => User::whereHas('role', function ($query) {
                $query->where('slug', 'manager');
            })->get(),
            'custom' => User::whereIn('id', $this->custom_recipients ?? [])->get(),
            default => collect(),
        };
    }

    /**
     * Get recipients based on role (stored in custom_recipients).
     */
    protected function getRoleRecipients()
    {
        if (empty($this->custom_recipients)) {
            return collect();
        }
        return User::whereIn('id', $this->custom_recipients)->get();
    }

    /**
     * Get recipients based on department.
     */
    protected function getDepartmentRecipients()
    {
        if (!$this->department_id) {
            return collect();
        }
        return User::where('department_id', $this->department_id)->get();
    }

    /**
     * Get custom user recipients.
     */
    protected function getCustomUserRecipients()
    {
        if (empty($this->custom_recipients)) {
            return collect();
        }
        return User::whereIn('id', $this->custom_recipients)->get();
    }
}
