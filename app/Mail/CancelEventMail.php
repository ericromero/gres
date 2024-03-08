<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CancelEventMail extends Mailable
{
    use Queueable, SerializesModels;

    public $event;
    public $cancellationReason;

    /**
     * Create a new message instance.
     */
    public function __construct($event, $cancellationReason)
    {
        $this->event = $event;
        $this->cancellationReason = $cancellationReason;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Evento Cancelado',
        );
    }

    public function build()
    {
        return $this->subject('Evento Cancelado: ' . $this->event->title)
            ->markdown('emails.cancel-event')
            ->with([
                'cancellationReason' => $this->cancellationReason,
            ]);
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
