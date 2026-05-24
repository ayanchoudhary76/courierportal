<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\TicketMessage;
use App\Services\TicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index()
    {
        $client  = Auth::user()->client;
        $tickets = $client
            ? $client->supportTickets()->latest()->paginate(10)
            : collect();

        return view('client.tickets.index', compact('tickets'));
    }

    public function create()
    {
        $recentBookings = Auth::user()->client
            ? Auth::user()->client->bookings()->latest()->limit(20)->pluck('awb_number')
            : collect();

        return view('client.tickets.create', compact('recentBookings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category'    => ['required', 'in:delayed_shipment,damage,wrong_delivery,invoice_issue,rate_query,other'],
            'awb_number'  => ['nullable', 'string', 'max:20'],
            'subject'     => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:20'],
            'attachment'  => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $filePath = null;
        if ($request->hasFile('attachment')) {
            $filePath = $request->file('attachment')
                ->store('tickets', env('FILESYSTEM_DISK', 'public'));
        }

        $ticket = SupportTicket::create([
            'client_id'     => Auth::user()->client->id,
            'ticket_number' => TicketService::generateNumber(),
            'category'      => $validated['category'],
            'awb_number'    => $validated['awb_number'] ?? null,
            'subject'       => $validated['subject'],
            'description'   => $validated['description'],
            'file_path'     => $filePath,
            'status'        => 'open',
        ]);

        return redirect()
            ->route('client.tickets.show', $ticket)
            ->with('success', "Ticket #{$ticket->ticket_number} created. We will respond within 24-48 hours.");
    }

    public function show(SupportTicket $ticket)
    {
        abort_if($ticket->client_id !== Auth::user()->client?->id, 403);
        $ticket->load('messages.sender');

        return view('client.tickets.show', compact('ticket'));
    }

    public function addMessage(Request $request, SupportTicket $ticket)
    {
        abort_if($ticket->client_id !== Auth::user()->client?->id, 403);
        $request->validate(['message' => ['required', 'string', 'min:2', 'max:2000']]);

        TicketMessage::create([
            'ticket_id'   => $ticket->id,
            'sender_role' => 'client',
            'sender_id'   => Auth::id(),
            'message'     => $request->message,
            'is_internal' => false,
        ]);

        $ticket->update(['status' => 'inprogress']);

        return back()->with('success', 'Reply sent.');
    }
}
