@component('mail::message')
# Reply to Your Ticket #{{ $ticket->ticket_number }}

**Subject:** {{ $ticket->subject }}

---

Our support team has responded to your ticket:

> {{ $message->message }}

---

@component('mail::button', ['url' => url('/client/tickets/' . $ticket->id), 'color' => 'primary'])
View Ticket
@endcomponent

You can also reply directly from the ticket page. We aim to resolve all tickets within 24–48 hours.

Thanks,<br>
**{{ config('app.name') }} Support Team**

<small>If you did not raise this ticket, please ignore this email.</small>
@endcomponent
