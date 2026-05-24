<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\AuditHelper;
use App\Http\Controllers\Controller;
use App\Mail\TicketReplyMail;
use App\Models\SupportTicket;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = SupportTicket::with('client.user', 'assignedTo')
            ->latest()
            ->when(request('status'),   fn ($q, $s) => $q->where('status', $s))
            ->when(request('category'), fn ($q, $c) => $q->where('category', $c))
            ->paginate(20);

        $statusCounts = SupportTicket::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return view('admin.tickets.index', compact('tickets', 'statusCounts'));
    }

    public function show(SupportTicket $ticket)
    {
        $ticket->load('client.user', 'messages.sender', 'assignedTo');
        $admins = User::where('role', 'admin')->get();

        return view('admin.tickets.show', compact('ticket', 'admins'));
    }

    public function addMessage(Request $request, SupportTicket $ticket)
    {
        $request->validate(['message' => ['required', 'string']]);

        $ticketMessage = TicketMessage::create([
            'ticket_id'   => $ticket->id,
            'sender_role' => 'admin',
            'sender_id'   => Auth::id(),
            'message'     => $request->message,
            'is_internal' => (bool) $request->internal_note,
        ]);

        if (! $request->internal_note) {
            $ticket->update(['status' => 'inprogress']);
            // Queue email notification to client
            try {
                Mail::to($ticket->client->user->email)
                    ->queue(new TicketReplyMail($ticket, $ticketMessage));
            } catch (\Throwable $e) {
                // Mail failure should not break the request
                logger()->error('TicketReplyMail failed: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Reply sent.');
    }

    public function updateStatus(Request $request, SupportTicket $ticket)
    {
        $request->validate(['status' => ['required', 'in:open,inprogress,resolved,closed']]);
        $ticket->update(['status' => $request->status]);

        AuditHelper::log('ticket_status_update', 'support_tickets', $ticket->id, [], ['status' => $request->status]);

        return response()->json(['success' => true]);
    }

    public function assign(Request $request, SupportTicket $ticket)
    {
        $request->validate(['admin_id' => ['required', 'exists:users,id']]);
        $ticket->update(['assigned_to' => $request->admin_id]);

        return response()->json(['success' => true]);
    }
}
