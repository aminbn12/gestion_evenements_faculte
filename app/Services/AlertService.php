<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\AlertLog;
use App\Jobs\SendEmailAlertJob;
use App\Jobs\SendWhatsAppAlertJob;

class AlertService
{
    /**
     * Send an alert to recipients.
     */
    public function sendAlert(Alert $alert): void
    {
        // Update status to sending
        $alert->update(['status' => 'sending']);

        try {
            $recipients = $alert->getRecipients();

            foreach ($recipients as $recipient) {
                // Send email
                if ($alert->send_email) {
                    $this->sendEmail($alert, $recipient);
                }

                // Send WhatsApp
                if ($alert->send_whatsapp && $recipient->phone) {
                    $this->sendWhatsApp($alert, $recipient);
                }
            }

            // Update alert status
            $alert->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

        } catch (\Exception $e) {
            $alert->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send email alert.
     */
    protected function sendEmail(Alert $alert, $recipient): void
    {
        $log = AlertLog::create([
            'alert_id' => $alert->id,
            'user_id' => $recipient->id,
            'channel' => 'email',
            'status' => 'pending',
        ]);

        try {
            // Dispatch job to queue
            SendEmailAlertJob::dispatch($alert, $recipient, $log);

        } catch (\Exception $e) {
            $log->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send WhatsApp alert.
     */
    protected function sendWhatsApp(Alert $alert, $recipient): void
    {
        $log = AlertLog::create([
            'alert_id' => $alert->id,
            'user_id' => $recipient->id,
            'channel' => 'whatsapp',
            'status' => 'pending',
        ]);

        try {
            // Dispatch job to queue
            SendWhatsAppAlertJob::dispatch($alert, $recipient, $log);

        } catch (\Exception $e) {
            $log->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Process scheduled alerts.
     */
    public function processScheduledAlerts(): int
    {
        $alerts = Alert::where('status', 'scheduled')
            ->where('scheduled_at', '<=', now())
            ->get();

        $processed = 0;

        foreach ($alerts as $alert) {
            $this->sendAlert($alert);
            $processed++;
        }

        return $processed;
    }
}
