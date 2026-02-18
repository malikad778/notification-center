<?php

namespace App\Mail;

use malikad778\NotificationCenter\DTOs\NotificationPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GenericNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public NotificationPayload $payload
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->payload->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'notification-center::emails.notification',
            with: [
                'payload' => $this->payload,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
