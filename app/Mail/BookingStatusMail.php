<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingStatusMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Booking $booking,
        public readonly string $newStatus
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Shipment Update — AWB {$this->booking->awb_number}: " . str_replace('_', ' ', ucwords($this->newStatus)),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.booking-status',
        );
    }
}
