<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Client;
use App\Models\RateCard;
use App\Models\RateMatrix;
use App\Models\SupportTicket;
use App\Models\TrackingEvent;
use App\Models\User;
use App\Services\AwbService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // ─── 1. Rate Card with full matrix ────────────────────────────
        $rateCard = RateCard::firstOrCreate(
            ['name' => 'Standard Domestic'],
            ['description' => 'Standard domestic rate card for all zones', 'is_default' => true, 'is_active' => true, 'created_by' => 1]
        );

        // Weight slabs: [from, to, multiplier]
        $slabs = [
            [0,    0.5,  1.0],
            [0.5,  1,    1.0],
            [1,    2,    1.5],
            [2,    3,    2.0],
            [3,    5,    2.5],
            [5,    7,    3.0],
            [7,    10,   4.0],
            [10,   15,   5.0],
            [15,   20,   6.0],
            [20,   999,  8.0],
        ];

        $baseRates = [
            'express_air'      => ['A'=>150, 'B'=>200, 'C'=>280, 'D'=>380, 'E'=>500],
            'priority_surface' => ['A'=>80,  'B'=>120, 'C'=>160, 'D'=>220, 'E'=>320],
            'economy_surface'  => ['A'=>60,  'B'=>90,  'C'=>120, 'D'=>170, 'E'=>250],
        ];

        foreach ($baseRates as $service => $zoneRates) {
            foreach ($zoneRates as $zone => $baseRate) {
                foreach ($slabs as [$from, $to, $mult]) {
                    RateMatrix::firstOrCreate([
                        'rate_card_id' => $rateCard->id,
                        'service_type' => $service,
                        'zone_code'    => $zone,
                        'weight_from'  => $from,
                        'weight_to'    => $to,
                    ], [
                        'base_rate'           => round($baseRate * $mult, 2),
                        'fuel_surcharge_pct'  => 12,
                        'oda_flat'            => $zone === 'E' ? 150 : 0,
                        'cod_pct'             => 0,
                    ]);
                }
            }
        }

        // ─── 2. Test Clients ──────────────────────────────────────────
        $clientsData = [
            [
                'user'   => ['name'=>'StyleCart Delhi','email'=>'stylecart@test.com','phone'=>'9111111111'],
                'client' => ['company_name'=>'StyleCart Pvt Ltd','address'=>'Block A, Connaught Place','city'=>'Delhi','pincode'=>'110001','state'=>'Delhi','account_type'=>'prepaid','credit_limit'=>0],
            ],
            [
                'user'   => ['name'=>'TechSupplies Mumbai','email'=>'techsupplies@test.com','phone'=>'9222222222'],
                'client' => ['company_name'=>'TechSupplies Co.','address'=>'Bandra Kurla Complex','city'=>'Mumbai','pincode'=>'400001','state'=>'Maharashtra','account_type'=>'credit','credit_limit'=>50000],
            ],
            [
                'user'   => ['name'=>'Handcraft Jaipur','email'=>'handcraft@test.com','phone'=>'9333333333'],
                'client' => ['company_name'=>'Handcraft India','address'=>'Pink City Market','city'=>'Jaipur','pincode'=>'302001','state'=>'Rajasthan','account_type'=>'prepaid','credit_limit'=>0],
            ],
        ];

        $clients = [];
        foreach ($clientsData as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['user']['email']],
                array_merge($data['user'], ['password' => Hash::make('Client@123456'), 'role' => 'client', 'is_active' => true])
            );
            $client = Client::firstOrCreate(
                ['user_id' => $user->id],
                array_merge($data['client'], ['rate_card_id' => $rateCard->id, 'is_active' => true, 'created_by_admin' => 1])
            );
            $clients[] = $client;
        }

        $styleCart = $clients[0];

        // ─── 3. Test Bookings for StyleCart ───────────────────────────
        $bookingsData = [
            ['origin'=>'110001','dest'=>'400001','service'=>'express_air',     'weight'=>1.5,'status'=>'delivered',        'payment_status'=>'paid',    'total'=>500,  'events'=>['booked','delivered']],
            ['origin'=>'110001','dest'=>'560001','service'=>'priority_surface','weight'=>3.0,'status'=>'in_transit',       'payment_status'=>'pending', 'total'=>350,  'events'=>['booked','picked_up']],
            ['origin'=>'110001','dest'=>'600001','service'=>'economy_surface', 'weight'=>0.5,'status'=>'booked',           'payment_status'=>'pending', 'total'=>120,  'events'=>['booked']],
            ['origin'=>'110001','dest'=>'700001','service'=>'express_air',     'weight'=>5.0,'status'=>'pickup_scheduled', 'payment_status'=>'pending', 'total'=>900,  'events'=>['booked','pickup_scheduled']],
            ['origin'=>'110001','dest'=>'302001','service'=>'priority_surface','weight'=>2.0,'status'=>'delivered',        'payment_status'=>'paid',    'total'=>280,  'events'=>['booked','picked_up','in_transit','delivered']],
        ];

        foreach ($bookingsData as $bd) {
            $awb = AwbService::generate();

            $booking = Booking::firstOrCreate(['awb_number' => $awb], [
                'client_id'          => $styleCart->id,
                'sender_name'        => $styleCart->user->name,
                'sender_address'     => $styleCart->address,
                'sender_pincode'     => $bd['origin'],
                'sender_phone'       => $styleCart->user->phone,
                'receiver_name'      => 'Test Receiver',
                'receiver_address'   => '123, Test Street',
                'receiver_pincode'   => $bd['dest'],
                'receiver_phone'     => '9000000000',
                'service_type'       => $bd['service'],
                'parcel_type'        => 'non_document',
                'weight_actual'      => $bd['weight'],
                'weight_volumetric'  => 0,
                'declared_value'     => 500,
                'pieces'             => 1,
                'base_amount'        => round($bd['total'] / 1.18, 2),
                'surcharges'         => ['fuel_charge'=>0,'fuel_pct'=>12,'oda_charge'=>0,'gst'=>round($bd['total'] - $bd['total']/1.18, 2),'zone'=>'D'],
                'total_amount'       => $bd['total'],
                'payment_mode'       => 'bill_to_account',
                'payment_status'     => $bd['payment_status'],
                'booking_status'     => $bd['status'],
            ]);

            foreach ($bd['events'] as $i => $eventType) {
                TrackingEvent::firstOrCreate([
                    'booking_id' => $booking->id,
                    'event_type' => $eventType,
                ], [
                    'location'   => $eventType === 'booked' ? 'Delhi - ' . $bd['origin'] : 'Transit Hub',
                    'remarks'    => ucwords(str_replace('_', ' ', $eventType)) . ' successfully',
                    'event_time' => now()->subDays(5 - $i),
                    'created_by' => 1,
                ]);
            }
        }

        // ─── 4. Support Tickets ───────────────────────────────────────
        SupportTicket::firstOrCreate(
            ['ticket_number' => 'TKT-' . date('Ym') . '-0001'],
            [
                'client_id'   => $styleCart->id,
                'category'    => 'delayed_shipment',
                'subject'     => 'Shipment not picked up yet',
                'description' => 'We booked a shipment 3 days ago but no pickup has happened yet. Please look into this urgently.',
                'status'      => 'open',
            ]
        );

        SupportTicket::firstOrCreate(
            ['ticket_number' => 'TKT-' . date('Ym') . '-0002'],
            [
                'client_id'   => $styleCart->id,
                'category'    => 'invoice_issue',
                'subject'     => 'Invoice amount mismatch',
                'description' => 'The invoice generated for last month shows incorrect amounts. The charged amount does not match the agreed rate card.',
                'status'      => 'resolved',
            ]
        );

        $this->command->info('TestDataSeeder completed successfully!');
        $this->command->info('  Rate card: Standard Domestic with ' . RateMatrix::where('rate_card_id', $rateCard->id)->count() . ' matrix rows');
        $this->command->info('  Clients: ' . count($clients) . ' test clients created');
        $this->command->info('  Bookings: ' . count($bookingsData) . ' bookings for StyleCart');
        $this->command->info('  Tickets: 2 support tickets created');
        $this->command->info('  Login: stylecart@test.com / Client@123456');
    }
}
