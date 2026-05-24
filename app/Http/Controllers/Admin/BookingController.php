<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AdminBookingsExport;
use App\Helpers\AuditHelper;
use App\Http\Controllers\Controller;
use App\Mail\BookingStatusMail;
use App\Models\Booking;
use App\Models\TrackingEvent;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $bookings = Booking::with('client.user')
            ->latest()
            ->when($request->date_from, fn ($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->date_to,   fn ($q) => $q->whereDate('created_at', '<=', $request->date_to))
            ->when($request->status,    fn ($q) => $q->where('booking_status', $request->status))
            ->when($request->service_type, fn ($q) => $q->where('service_type', $request->service_type))
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($inner) use ($request) {
                    $inner->where('awb_number', 'like', '%' . $request->search . '%')
                          ->orWhereHas('client.user', fn ($u) => $u->where('name', 'like', '%' . $request->search . '%'));
                });
            })
            ->paginate(20)
            ->withQueryString();

        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        $booking->load('client.user', 'trackingEvents.creator');

        return view('admin.bookings.show', compact('booking'));
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'status'            => ['required', 'in:booked,pickup_scheduled,picked_up,in_transit,out_for_delivery,delivered,failed,returned'],
            'tracking_location' => ['nullable', 'string', 'max:150'],
            'tracking_remarks'  => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($request, $booking) {
            $booking->update(['booking_status' => $request->status]);

            TrackingEvent::create([
                'booking_id' => $booking->id,
                'event_type' => $request->status,
                'location'   => $request->tracking_location,
                'remarks'    => $request->tracking_remarks,
                'event_time' => now(),
                'created_by' => Auth::id(),
            ]);

            AuditHelper::log('booking_status_update', 'bookings', $booking->id, [], ['status' => $request->status]);
        });

        // Queue email notification to client
        try {
            Mail::to($booking->client->user->email)
                ->queue(new BookingStatusMail($booking, $request->status));
        } catch (\Throwable $e) {
            logger()->error('BookingStatusMail failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Booking status updated to: ' . str_replace('_', ' ', ucwords($request->status)));
    }

    public function addTracking(Request $request, Booking $booking)
    {
        $request->validate([
            'event_type' => ['required', 'string', 'max:50'],
            'location'   => ['nullable', 'string', 'max:150'],
            'remarks'    => ['nullable', 'string', 'max:500'],
            'event_time' => ['required', 'date'],
        ]);

        TrackingEvent::create([
            'booking_id' => $booking->id,
            'event_type' => $request->event_type,
            'location'   => $request->location,
            'remarks'    => $request->remarks,
            'event_time' => $request->event_time,
            'created_by' => Auth::id(),
        ]);

        return back()->with('success', 'Tracking event added.');
    }

    public function printLabel(Booking $booking)
    {
        $pdf = Pdf::loadView('client.label-pdf', ['booking' => $booking])
            ->setPaper([0, 0, 283.46, 425.2])
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => false,
                'defaultFont'          => 'dejavu sans',
                'dpi'                  => 96,
            ]);

        return $pdf->download('AWB-' . $booking->awb_number . '.pdf');
    }

    public function export(Request $request)
    {
        $from = $request->date_from ?? now()->subDays(30)->format('Y-m-d');
        $to   = $request->date_to   ?? now()->format('Y-m-d');

        return Excel::download(
            new AdminBookingsExport($from, $to, $request->status, $request->service_type, $request->client_id ? (int)$request->client_id : null),
            'admin-bookings-' . $from . '-to-' . $to . '.xlsx'
        );
    }
}
