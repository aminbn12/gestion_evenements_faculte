<?php

namespace App\Console\Commands;

use App\Models\Alert;
use App\Models\Event;
use Illuminate\Console\Command;

class SendEventReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send automatic reminders for upcoming events';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting event reminder check...');

        // Configurable reminder days: 1 month (30 days), 10 days, 1 day
        $reminderDays = [30, 10, 1];

        $eventsReminded = 0;

        foreach ($reminderDays as $days) {
            $this->info("Checking for events in $days day(s)...");

            // Find events that need reminders for this day
            $events = Event::where('auto_reminder_enabled', true)
                ->whereDate('start_date', now()->addDays($days))
                ->where('status', 'published')
                ->with(['creator', 'department'])
                ->get();

            foreach ($events as $event) {
                // Check if alert already exists for this reminder
                $exists = Alert::where('event_id', $event->id)
                    ->where('reminder_days', $days)
                    ->where('status', '!=', 'failed')
                    ->exists();

                if (!$exists) {
                    $this->info("Creating reminder for event: {$event->title} (in $days days)");

                    Alert::create([
                        'event_id' => $event->id,
                        'created_by' => $event->created_by,
                        'subject' => "Rappel: {$event->title} dans $days jour(s)",
                        'message' => "L'événement \"{$event->title}\" commence dans $days jour(s).\n\n" .
                                    "📅 Date: " . $event->start_date->format('d/m/Y H:i') . "\n" .
                                    "📍 Lieu: " . ($event->location ?? 'Non défini') . "\n" .
                                    "🏢 Type: " . ucfirst($event->type) . "\n" .
                                    "⚡ Priorité: " . ucfirst($event->priority),
                        'send_email' => true,
                        'send_whatsapp' => false,
                        'recipient_type' => 'all', // Send to all relevant users
                        'custom_recipients' => null,
                        'scheduled_at' => now(),
                        'reminder_days' => $days,
                        'status' => 'pending',
                    ]);

                    $eventsReminded++;
                }
            }
        }

        // Also send alerts that are scheduled and pending
        $this->info('Processing scheduled alerts...');
        $scheduledAlerts = Alert::where('status', 'pending')
            ->where('scheduled_at', '<=', now())
            ->get();

        foreach ($scheduledAlerts as $alert) {
            try {
                $alertService = app(\App\Services\AlertService::class);
                $alertService->sendAlert($alert);
                $this->info("Sent alert: {$alert->subject}");
            } catch (\Exception $e) {
                $this->error("Failed to send alert: {$e->getMessage()}");
            }
        }

        $this->info("Reminder process completed. Created $eventsReminded new reminders.");

        return Command::SUCCESS;
    }
}
