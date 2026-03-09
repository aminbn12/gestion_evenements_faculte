<?php

namespace App\Mail;

use App\Models\Alert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventAlertMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected Alert $alert;
    protected $recipient;

    /**
     * Create a new message instance.
     */
    public function __construct(Alert $alert, $recipient)
    {
        $this->alert = $alert;
        $this->recipient = $recipient;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->alert->subject,
            from: config('mail.from.address'),
            to: $this->recipient->email,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.alert-template',
            with: [
                'alert' => $this->alert,
                'event' => $this->alert->event,
                'recipient' => $this->recipient,
            ],
        );
    }
}
