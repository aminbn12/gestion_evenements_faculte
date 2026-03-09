<?php

namespace App\Jobs;

use App\Models\Alert;
use App\Models\AlertLog;
use App\Mail\EventAlertMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailAlertJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels, Queueable;

    protected Alert $alert;
    protected $recipient;
    protected AlertLog $log;

    /**
     * Create a new job instance.
     */
    public function __construct(Alert $alert, $recipient, AlertLog $log)
    {
        $this->alert = $alert;
        $this->recipient = $recipient;
        $this->log = $log;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Mail::to($this->recipient->email, new EventAlertMail($this->alert, $this->recipient));

            $this->log->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

        } catch (\Exception $e) {
                $this->log->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);

                // Release job back to queue after 30 seconds
                $this->release(30);
        }
    }
}
