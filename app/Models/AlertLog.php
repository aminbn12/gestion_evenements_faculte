<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlertLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'alert_id',
        'user_id',
        'channel',
        'status',
        'error_message',
        'message_id',
        'sent_at',
        'delivered_at',
        'opened_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'opened_at' => 'datetime',
    ];

    /**
     * Get the alert that owns the log.
     */
    public function alert()
    {
        return $this->belongsTo(Alert::class);
    }

    /**
     * Get the user that owns the log.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if log is sent.
     */
    public function isSent(): bool
    {
        return $this->status === 'sent';
    }

    /**
     * Check if log is failed.
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }
}
