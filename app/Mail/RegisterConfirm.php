<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegisterConfirm extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $tries = 3;

    public $backoff = 30;

    public $timeout = 60;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $user,
        public string $url,
        public int $expireHours = 24
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [
                new Address('taylor@example.com', 'Taylor Otwell'),
            ],
            subject: 'Register Confirm',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // throw new \Exception("test queue fail");
        return new Content(
            markdown: 'emails.register',
            with: [
                'user' => $this->user,
                'url' => $this->url,
                'expireHours' => $this->expireHours,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
