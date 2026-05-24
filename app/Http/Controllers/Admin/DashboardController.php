<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Client;
use App\Models\SupportTicket;

class DashboardController extends Controller
{
    public function index()
    {
        $pageTitle = 'Dashboard';

        $stats = [
            // Row 1
            'todayBookings'    => Booking::whereDate('created_at', today())->count(),
            'pendingPickups'   => Booking::where('booking_status', 'pickup_scheduled')->count(),
            'inTransit'        => Booking::where('booking_status', 'in_transit')->count(),
            'deliveredToday'   => Booking::where('booking_status', 'delivered')
                                         ->whereDate('updated_at', today())->count(),
            // Row 2
            'openTickets'      => SupportTicket::whereIn('status', ['open', 'inprogress'])->count(),
            'revenueToday'     => Booking::whereDate('created_at', today())
                                         ->where('payment_status', 'paid')
                                         ->sum('total_amount'),
            'newClientsWeek'   => Client::whereBetween('created_at', [
                                      now()->startOfWeek(), now()->endOfWeek(),
                                  ])->count(),
            'failedDeliveries' => Booking::where('booking_status', 'failed')
                                         ->whereDate('updated_at', today())->count(),

            // Extras for sidebar context
            'totalClients'     => Client::where('is_active', true)->count(),

            // Financial
            'outstandingBills' => Booking::where('payment_status', '!=', 'paid')->sum('total_amount'),
            'totalRevenue'     => Booking::where('payment_status', 'paid')->sum('total_amount'),
        ];

        $recentBookings = Booking::with('client.user')->latest()->limit(8)->get();

        return view('admin.dashboard', compact('pageTitle', 'stats', 'recentBookings'));
    }
}
