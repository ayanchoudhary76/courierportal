<?php

namespace App\Mail;

use App\Models\SupportTicket;
use App\Models\TicketMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketReplyMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly SupportTicket $ticket,
        public readonly TicketMessage $message
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Reply to Ticket #{$this->ticket->ticket_number}: {$this->ticket->subject}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.ticket-reply',
        );
    }
}
