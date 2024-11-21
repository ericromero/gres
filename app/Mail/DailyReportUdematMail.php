<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailyReportUdematMail extends Mailable
{
    use Queueable, SerializesModels;

    public $eventCount;
    public $eventData;

    /**
     * Create a new message instance.
     */
    public function __construct($eventCount, $eventData)
    {
        $this->eventCount = $eventCount;
        $this->eventData = $eventData;
    }

    public function build()
    {
        return $this->subject('Eventos que solicitan servicios UDEMAT - Reporte diario')
            ->markdown('emails.daily-report-udemat');
    }

    /**
     * Get the message envelope.
     */
    // public function envelope(): Envelope
    // {
    //     return new Envelope(
    //         subject: 'Daily Report Udemat Mail',
    //     );
    // }

    /**
     * Get the message content definition.
     */
    // public function content(): Content
    // {
    //     return new Content(
    //         markdown: 'emails.daily-report-udemat',
    //     );
    // }

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
