<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'approved_by',
        'type',
        'reason',
        'start_date',
        'end_date',
        'days_count',
        'is_half_day',
        'status',
        'rejection_reason',
        'approved_at',
        'attachments',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
        'is_half_day' => 'boolean',
        'attachments' => 'array',
    ];

    /**
     * Get the user that owns the leave.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who approved the leave.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Check if leave is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if leave is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if leave is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Get the type label.
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'annual' => 'Congé Annuel',
            'sick' => 'Congé Maladie',
            'maternity' => 'Congé Maternité',
            'paternity' => 'Congé Paternité',
            'study' => 'Congé d\'Études',
            'unpaid' => 'Congé Sans Solde',
            'other' => 'Autre',
            default => $this->type,
        };
    }

    /**
     * Get the status badge class.
     */
    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'pending' => 'badge-warning',
            'approved' => 'badge-success',
            'rejected' => 'badge-danger',
            'cancelled' => 'badge-secondary',
            default => 'badge-secondary',
        };
    }
}
