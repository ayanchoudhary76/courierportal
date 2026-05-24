@component('mail::message')
# Shipment Status Update

Your shipment **{{ $booking->awb_number }}** has been updated.

@component('mail::panel')
**New Status:** {{ str_replace('_', ' ', ucwords($newStatus)) }}

**Route:** {{ $booking->sender_pincode }} → {{ $booking->receiver_pincode }}

**Service:** {{ str_replace('_', ' ', ucwords($booking->service_type)) }}
@endcomponent

@component('mail::button', ['url' => url('/track/' . $booking->awb_number), 'color' => 'primary'])
Track Your Shipment
@endcomponent

---

**Estimated Transit Times:**
- Express Air: 1–2 business days
- Priority Surface: 3–5 business days
- Economy Surface: 5–7 business days

If you have any concerns, please [raise a support ticket]({{ url('/client/tickets/create') }}).

Thanks,<br>
**{{ config('app.name') }} Delivery Team**
@endcomponent
