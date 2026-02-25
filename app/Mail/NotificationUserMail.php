<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class NotificationUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public $user
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'User Notification',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.notification-user',
            with: [
                'user' => $this->user,
            ],
        );
    }
}