<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Client;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        $now = now();

        return view('admin.reports.index', [
            'total_bookings_this_month' => Booking::whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->count(),
            'total_revenue_this_month'  => Booking::whereMonth('created_at', $now->month)->whereYear('created_at', $now->year)->sum('total_amount'),
            'total_clients'             => Client::where('is_active', true)->count(),
            'active_tickets'            => SupportTicket::whereIn('status', ['open', 'inprogress'])->count(),
        ]);
    }

    public function bookings(Request $request)
    {
        $from = $request->date_from ? Carbon::parse($request->date_from) : now()->subDays(30);
        $to   = $request->date_to   ? Carbon::parse($request->date_to)   : now();

        $query = Booking::with('client.user')
            ->whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()])
            ->when($request->status,       fn ($q) => $q->where('booking_status', $request->status))
            ->when($request->service_type, fn ($q) => $q->where('service_type', $request->service_type))
            ->when($request->client_id,    fn ($q) => $q->where('client_id', $request->client_id))
            ->latest();

        $statusCounts = (clone $query->getQuery())
            ->selectRaw('booking_status as status, count(*) as total')
            ->groupBy('booking_status')
            ->pluck('total', 'status');

        $bookings = $query->paginate(25)->withQueryString();
        $clients  = Client::with('user')->get();

        return view('admin.reports.bookings', compact('bookings', 'statusCounts', 'from', 'to', 'clients'));
    }

    public function revenue(Request $request)
    {
        $from = $request->date_from ? Carbon::parse($request->date_from) : now()->subDays(30);
        $to   = $request->date_to   ? Carbon::parse($request->date_to)   : now();

        $daily = Booking::selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue, COUNT(*) as count')
            ->whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()])
            ->when($request->client_id, fn ($q) => $q->where('client_id', $request->client_id))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $topClients = Client::withSum('bookings', 'total_amount')
            ->withCount('bookings')
            ->with('user')
            ->orderByDesc('bookings_sum_total_amount')
            ->limit(10)
            ->get();

        $clients = Client::with('user')->get();

        return view('admin.reports.revenue', compact('daily', 'topClients', 'from', 'to', 'clients'));
    }

    public function clients(Request $request)
    {
        $clients = Client::withCount('bookings')
            ->withSum('bookings', 'total_amount')
            ->with('user')
            ->when($request->search, function ($q) use ($request) {
                $q->whereHas('user', fn ($u) => $u->where('name', 'like', '%' . $request->search . '%'))
                  ->orWhere('company_name', 'like', '%' . $request->search . '%');
            })
            ->paginate(20)
            ->withQueryString();

        return view('admin.reports.clients', compact('clients'));
    }
}
