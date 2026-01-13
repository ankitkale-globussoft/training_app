<?php

namespace App\Mail;

use App\Models\Trainer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TrainerVerifiedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $trainer;

    /**
     * Create a new message instance.
     */
    public function __construct(Trainer $trainer)
    {
        $this->trainer = $trainer;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Account Verified - ' . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.trainer.verified',
            with: ['trainer' => $this->trainer],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
