<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function edit()
    {
        $user   = Auth::user()->load('client');
        $client = $user->client;

        $indianStates = [
            'Andhra Pradesh','Arunachal Pradesh','Assam','Bihar','Chhattisgarh',
            'Goa','Gujarat','Haryana','Himachal Pradesh','Jharkhand','Karnataka',
            'Kerala','Madhya Pradesh','Maharashtra','Manipur','Meghalaya','Mizoram',
            'Nagaland','Odisha','Punjab','Rajasthan','Sikkim','Tamil Nadu','Telangana',
            'Tripura','Uttar Pradesh','Uttarakhand','West Bengal',
            'Andaman and Nicobar Islands','Chandigarh',
            'Dadra and Nagar Haveli and Daman and Diu',
            'Delhi','Jammu and Kashmir','Ladakh','Lakshadweep','Puducherry',
        ];

        return view('client.profile', compact('user', 'client', 'indianStates'));
    }

    public function update(Request $request)
    {
        $user = Auth::user()->load('client');

        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:100'],
            'phone'        => ['required', 'string', 'max:15'],
            'company_name' => ['required', 'string', 'max:150'],
            'gstin'        => ['nullable', 'string', 'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/'],
            'address'      => ['required', 'string'],
            'city'         => ['required', 'string', 'max:60'],
            'pincode'      => ['required', 'digits:6'],
            'state'        => ['required', 'string', 'max:60'],
        ]);

        DB::transaction(function () use ($user, $validated) {
            $user->update([
                'name'  => $validated['name'],
                'phone' => $validated['phone'],
            ]);

            $user->client?->update([
                'company_name' => $validated['company_name'],
                'gstin'        => $validated['gstin'] ?? null,
                'address'      => $validated['address'],
                'city'         => $validated['city'],
                'pincode'      => $validated['pincode'],
                'state'        => $validated['state'],
            ]);
        });

        return back()->with('success', 'Profile updated successfully.');
    }
}
