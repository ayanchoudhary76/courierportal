<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user   = Auth::user()->load('client.rateCard');
        $client = $user->client;

        if (! $client) {
            // Edge case: user has client role but no client record
            return view('client.dashboard', [
                'client'             => null,
                'totalBookings'      => 0,
                'activeShipments'    => 0,
                'deliveredCount'     => 0,
                'outstandingBalance' => 0,
                'recentBookings'     => collect(),
            ]);
        }

        return view('client.dashboard', [
            'client'             => $client,
            'totalBookings'      => $client->bookings()->count(),
            'activeShipments'    => $client->bookings()
                ->whereIn('booking_status', ['picked_up', 'in_transit', 'out_for_delivery'])
                ->count(),
            'deliveredCount'     => $client->bookings()
                ->where('booking_status', 'delivered')
                ->count(),
            'outstandingBalance' => $client->bookings()
                ->where('payment_status', '!=', 'paid')
                ->sum('total_amount'),
            'recentBookings'     => $client->bookings()
                ->latest()
                ->limit(5)
                ->get(),
        ]);
    }
}
