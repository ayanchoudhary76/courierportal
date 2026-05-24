<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\RateCard;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    // ──────────────────────────────────────────────────────────────────
    // INDEX
    // ──────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $search = $request->get('search');

        $clients = Client::with(['user', 'rateCard'])
            ->when($search, function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                  });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $rateCards = RateCard::all();

        return view('admin.clients.index', compact('clients', 'search', 'rateCards'));
    }

    // ──────────────────────────────────────────────────────────────────
    // CREATE
    // ──────────────────────────────────────────────────────────────────
    public function create()
    {
        $rateCards = RateCard::orderBy('name')->get();
        return view('admin.clients.create', compact('rateCards'));
    }

    // ──────────────────────────────────────────────────────────────────
    // STORE
    // ──────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:100'],
            'email'        => ['required', 'email', 'unique:users,email'],
            'phone'        => ['required', 'digits_between:10,15'],
            'password'     => ['required', 'min:8', 'confirmed'],
            'company_name' => ['required', 'string', 'max:150'],
            'gstin'        => ['nullable', 'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/'],
            'address'      => ['required', 'string'],
            'city'         => ['required', 'string'],
            'pincode'      => ['required', 'digits:6'],
            'state'        => ['required', 'string'],
            'account_type' => ['required', 'in:prepaid,credit'],
            'credit_limit' => ['required_if:account_type,credit', 'nullable', 'numeric', 'min:0'],
            'rate_card_id' => ['nullable', 'exists:rate_cards,id'],
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name'      => $validated['name'],
                'email'     => $validated['email'],
                'phone'     => $validated['phone'],
                'password'  => Hash::make($validated['password']),
                'role'      => User::ROLE_CLIENT,
                'is_active' => true,
            ]);

            Client::create([
                'user_id'          => $user->id,
                'company_name'     => $validated['company_name'],
                'gstin'            => $validated['gstin'] ?? null,
                'address'          => $validated['address'],
                'city'             => $validated['city'],
                'pincode'          => $validated['pincode'],
                'state'            => $validated['state'],
                'account_type'     => $validated['account_type'],
                'credit_limit'     => $validated['account_type'] === 'credit'
                                        ? ($validated['credit_limit'] ?? 0)
                                        : 0,
                'rate_card_id'     => $validated['rate_card_id'] ?? null,
                'created_by_admin' => Auth::id(),
                'is_active'        => true,
            ]);
        });

        return redirect()->route('admin.clients.index')
            ->with('success', 'Client created successfully.');
    }

    // ──────────────────────────────────────────────────────────────────
    // SHOW
    // ──────────────────────────────────────────────────────────────────
    public function show($id)
    {
        $client = Client::with([
            'user',
            'rateCard',
            'bookings' => fn ($q) => $q->latest()->limit(10),
            'supportTickets' => fn ($q) => $q->latest()->limit(5),
        ])->findOrFail($id);

        $totalBookings      = $client->bookings()->count();
        $totalRevenue       = $client->bookings()->sum('total_amount');
        $outstandingBalance = $client->bookings()
                                     ->whereIn('payment_status', ['pending', 'partial'])
                                     ->sum('total_amount');

        return view('admin.clients.show', compact(
            'client', 'totalBookings', 'totalRevenue', 'outstandingBalance'
        ));
    }

    // ──────────────────────────────────────────────────────────────────
    // EDIT
    // ──────────────────────────────────────────────────────────────────
    public function edit($id)
    {
        $client    = Client::with('user')->findOrFail($id);
        $rateCards = RateCard::orderBy('name')->get();

        return view('admin.clients.edit', compact('client', 'rateCards'));
    }

    // ──────────────────────────────────────────────────────────────────
    // UPDATE
    // ──────────────────────────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $client = Client::with('user')->findOrFail($id);

        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:100'],
            'email'        => ['required', 'email', 'unique:users,email,' . $client->user_id],
            'phone'        => ['required', 'digits_between:10,15'],
            'password'     => ['nullable', 'min:8', 'confirmed'],
            'company_name' => ['required', 'string', 'max:150'],
            'gstin'        => ['nullable', 'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/'],
            'address'      => ['required', 'string'],
            'city'         => ['required', 'string'],
            'pincode'      => ['required', 'digits:6'],
            'state'        => ['required', 'string'],
            'account_type' => ['required', 'in:prepaid,credit'],
            'credit_limit' => ['required_if:account_type,credit', 'nullable', 'numeric', 'min:0'],
            'rate_card_id' => ['nullable', 'exists:rate_cards,id'],
        ]);

        DB::transaction(function () use ($validated, $client) {
            $userFields = [
                'name'  => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
            ];
            if (! empty($validated['password'])) {
                $userFields['password'] = Hash::make($validated['password']);
            }
            $client->user->update($userFields);

            $client->update([
                'company_name' => $validated['company_name'],
                'gstin'        => $validated['gstin'] ?? null,
                'address'      => $validated['address'],
                'city'         => $validated['city'],
                'pincode'      => $validated['pincode'],
                'state'        => $validated['state'],
                'account_type' => $validated['account_type'],
                'credit_limit' => $validated['account_type'] === 'credit'
                                    ? ($validated['credit_limit'] ?? 0)
                                    : 0,
                'rate_card_id' => $validated['rate_card_id'] ?? null,
            ]);
        });

        return redirect()->route('admin.clients.show', $client->id)
            ->with('success', 'Client updated successfully.');
    }

    // ──────────────────────────────────────────────────────────────────
    // DESTROY  (soft-suspend only — no real delete)
    // ──────────────────────────────────────────────────────────────────
    public function destroy($id)
    {
        $client = Client::with('user')->findOrFail($id);

        DB::transaction(function () use ($client) {
            $client->update(['is_active' => false]);
            $client->user->update(['is_active' => false]);
        });

        return redirect()->route('admin.clients.index')
            ->with('success', 'Client suspended successfully.');
    }

    // ──────────────────────────────────────────────────────────────────
    // BOOKINGS (sub-page)
    // ──────────────────────────────────────────────────────────────────
    public function bookings($id)
    {
        $client   = Client::with('user')->findOrFail($id);
        $bookings = $client->bookings()->latest()->paginate(20);

        return view('admin.clients.bookings', compact('client', 'bookings'));
    }
}
