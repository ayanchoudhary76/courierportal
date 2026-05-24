@extends('client.layouts.app')
@section('page-title', 'Fast & Reliable Courier Service')

@push('styles')
<style>
:root { --blue:#2563eb; --indigo:#4f46e5; }
.container { max-width:1200px; margin:0 auto; padding:0 24px; }

/* Hero */
.hero { background:linear-gradient(135deg,#1e40af 0%,#4f46e5 100%); color:#fff; padding:100px 0 90px; text-align:center; position:relative; overflow:hidden; }
.hero::before { content:''; position:absolute; inset:0; background:url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E"); }
.hero-content { position:relative; z-index:1; }
.hero-badge { display:inline-flex; align-items:center; gap:6px; background:rgba(255,255,255,0.15); border:1px solid rgba(255,255,255,0.25); border-radius:20px; padding:5px 14px; font-size:12.5px; font-weight:600; margin-bottom:20px; }
.hero h1 { font-size:clamp(32px,5vw,56px); font-weight:800; line-height:1.15; margin-bottom:18px; letter-spacing:-0.02em; }
.hero h1 span { color:#fbbf24; }
.hero p { font-size:18px; color:rgba(255,255,255,0.8); max-width:560px; margin:0 auto 36px; line-height:1.6; }
.hero-btns { display:flex; gap:14px; justify-content:center; flex-wrap:wrap; }
.btn-hero-white { background:#fff; color:#1e40af; padding:14px 30px; border-radius:10px; font-size:15px; font-weight:700; text-decoration:none; transition:transform 0.15s,box-shadow 0.15s; display:inline-flex; align-items:center; gap:8px; }
.btn-hero-white:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(0,0,0,0.18); }
.btn-hero-outline { background:transparent; color:#fff; border:2px solid rgba(255,255,255,0.5); padding:14px 30px; border-radius:10px; font-size:15px; font-weight:700; text-decoration:none; transition:all 0.15s; display:inline-flex; align-items:center; gap:8px; }
.btn-hero-outline:hover { border-color:#fff; background:rgba(255,255,255,0.1); }

/* Stats */
.stats-strip { background:#f8fafc; border-top:1px solid #e2e8f0; border-bottom:1px solid #e2e8f0; padding:32px 0; }
.stats-grid { display:grid; grid-template-columns:repeat(5,1fr); gap:0; }
@media(max-width:768px) { .stats-grid { grid-template-columns:repeat(3,1fr); gap:24px; } }
@media(max-width:480px) { .stats-grid { grid-template-columns:repeat(2,1fr); } }
.stat-item { text-align:center; padding:0 20px; border-right:1px solid #e2e8f0; }
.stat-item:last-child { border-right:none; }
.stat-number { font-size:30px; font-weight:800; color:#1e40af; line-height:1; }
.stat-label { font-size:12.5px; color:#64748b; font-weight:500; margin-top:5px; }

/* Section commons */
section { padding:80px 0; }
.section-label { display:inline-block; font-size:12px; font-weight:700; color:var(--blue); letter-spacing:0.1em; text-transform:uppercase; background:#eff6ff; padding:4px 12px; border-radius:20px; margin-bottom:12px; }
.section-title { font-size:clamp(22px,3.5vw,36px); font-weight:800; color:#0f172a; margin-bottom:14px; line-height:1.25; }
.section-sub { font-size:16px; color:#64748b; max-width:540px; margin:0 auto; line-height:1.6; }
.text-center { text-align:center; }

/* Why Choose Us */
.pillars-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:24px; margin-top:48px; }
@media(max-width:768px) { .pillars-grid { grid-template-columns:repeat(2,1fr); } }
@media(max-width:480px) { .pillars-grid { grid-template-columns:1fr; } }
.pillar-card { background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:28px; transition:box-shadow 0.2s,transform 0.15s; }
.pillar-card:hover { box-shadow:0 8px 30px rgba(0,0,0,0.08); transform:translateY(-3px); }
.pillar-icon { font-size:36px; margin-bottom:14px; }
.pillar-title { font-size:15px; font-weight:700; color:#1e293b; margin-bottom:8px; }
.pillar-desc { font-size:13.5px; color:#64748b; line-height:1.6; }

/* How it Works */
.how-bg { background:#f8fafc; }
.steps-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:0; margin-top:56px; position:relative; }
@media(max-width:768px) { .steps-grid { grid-template-columns:1fr; gap:24px; } .step-arrow { display:none; } }
.step-card { text-align:center; padding:0 32px; position:relative; }
.step-card::after { content:'→'; position:absolute; right:-16px; top:30px; font-size:28px; color:#bfdbfe; font-weight:300; }
.step-card:last-child::after { display:none; }
.step-num { width:56px; height:56px; border-radius:50%; background:linear-gradient(135deg,#2563eb,#4f46e5); color:#fff; font-size:20px; font-weight:800; display:flex; align-items:center; justify-content:center; margin:0 auto 18px; box-shadow:0 4px 14px rgba(37,99,235,0.35); }
.step-icon { font-size:28px; margin-bottom:12px; }
.step-title { font-size:16px; font-weight:700; color:#1e293b; margin-bottom:8px; }
.step-desc { font-size:13.5px; color:#64748b; line-height:1.6; }

/* Track Widget */
.track-section { background:#fff; }
.track-card { background:#fff; border-radius:20px; border:1px solid #e2e8f0; box-shadow:0 4px 24px rgba(0,0,0,0.06); padding:48px; max-width:600px; margin:40px auto 0; text-align:center; }
.track-input-row { display:flex; gap:10px; margin-top:24px; }
.track-input { flex:1; padding:13px 18px; border:2px solid #e2e8f0; border-radius:10px; font-size:14px; outline:none; font-family:monospace; transition:border-color 0.2s; }
.track-input:focus { border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,0.1); }
.track-btn { padding:13px 28px; background:#2563eb; color:#fff; border:none; border-radius:10px; font-size:14px; font-weight:700; cursor:pointer; transition:background 0.15s; white-space:nowrap; font-family:inherit; }
.track-btn:hover { background:#1d4ed8; }

/* Testimonials */
.testi-bg { background:#f8fafc; }
.testi-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:24px; margin-top:48px; }
@media(max-width:768px) { .testi-grid { grid-template-columns:1fr; } }
.testi-card { background:#fff; border-radius:16px; border:1px solid #e2e8f0; padding:28px; box-shadow:0 2px 8px rgba(0,0,0,0.04); }
.stars { color:#fbbf24; font-size:16px; margin-bottom:14px; letter-spacing:2px; }
.testi-quote { font-size:14px; color:#475569; line-height:1.7; margin-bottom:20px; font-style:italic; }
.testi-author { display:flex; align-items:center; gap:12px; }
.testi-avatar { width:40px; height:40px; border-radius:50%; background:linear-gradient(135deg,#2563eb,#4f46e5); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:15px; flex-shrink:0; }
.testi-name { font-size:13px; font-weight:700; color:#1e293b; }
.testi-co { font-size:12px; color:#64748b; }

/* Contact/Maps */
.contact-grid { display:grid; grid-template-columns:1fr 1fr; gap:32px; margin-top:48px; }
@media(max-width:768px) { .contact-grid { grid-template-columns:1fr; } }
.contact-card { background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:32px; }
.contact-item { display:flex; align-items:flex-start; gap:14px; margin-bottom:20px; }
.contact-icon { width:38px; height:38px; border-radius:10px; background:#eff6ff; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.contact-label { font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:3px; }
.contact-value { font-size:14px; font-weight:600; color:#1e293b; }
.map-placeholder { background:linear-gradient(135deg,#f1f5f9,#e2e8f0); border-radius:16px; border:1px solid #e2e8f0; height:100%; min-height:300px; display:flex; flex-direction:column; align-items:center; justify-content:center; color:#94a3b8; text-align:center; padding:24px; }
.map-placeholder p { font-size:13px; margin-top:8px; }

/* FAQ */
.faq-list { max-width:720px; margin:40px auto 0; display:flex; flex-direction:column; gap:12px; }
.faq-item { background:#fff; border:1px solid #e2e8f0; border-radius:12px; overflow:hidden; }
.faq-trigger { width:100%; display:flex; align-items:center; justify-content:space-between; padding:18px 22px; background:none; border:none; cursor:pointer; font-size:14.5px; font-weight:600; color:#1e293b; text-align:left; font-family:inherit; }
.faq-trigger:hover { background:#f8fafc; }
.faq-chevron { transition:transform 0.2s; flex-shrink:0; }
.faq-body { font-size:13.5px; color:#64748b; line-height:1.7; padding:0 22px 18px; }

/* CTA Banner */
.cta-banner { background:linear-gradient(135deg,#1e40af,#4f46e5); color:#fff; padding:80px 0; text-align:center; }
.cta-banner h2 { font-size:clamp(24px,4vw,40px); font-weight:800; margin-bottom:12px; }
.cta-banner p { font-size:16px; color:rgba(255,255,255,0.8); margin-bottom:36px; }
.cta-btns { display:flex; gap:14px; justify-content:center; flex-wrap:wrap; }
.btn-cta-white { background:#fff; color:#1e40af; padding:14px 30px; border-radius:10px; font-size:15px; font-weight:700; text-decoration:none; transition:all 0.15s; }
.btn-cta-white:hover { box-shadow:0 6px 20px rgba(0,0,0,0.2); }
.btn-cta-outline { border:2px solid rgba(255,255,255,0.5); color:#fff; padding:14px 30px; border-radius:10px; font-size:15px; font-weight:700; text-decoration:none; transition:all 0.15s; }
.btn-cta-outline:hover { border-color:#fff; background:rgba(255,255,255,0.1); }
[x-cloak] { display:none!important; }
</style>
@endpush

@section('content')

{{-- ── Section 1: Hero ──────────────────────────────────────────────── --}}
<section class="hero" style="padding-top:90px;padding-bottom:80px">
    <div class="container hero-content">
        <div class="hero-badge">🏆 India's Trusted Courier Network</div>
        <h1>Delivering <span>Trust</span><br>Since 1989</h1>
        <p>35+ Years of Reliable Logistics — Pan-India & International Shipping with Real-Time Tracking</p>
        <div class="hero-btns">
            @auth
            <a href="{{ route('client.book') }}" class="btn-hero-white">📦 Book Now</a>
            <a href="{{ route('client.rates') }}" class="btn-hero-outline">💰 Get a Quote</a>
            @else
            <a href="{{ route('client.register') }}" class="btn-hero-white">✨ Get Started Free</a>
            <a href="{{ route('tracking.public') }}" class="btn-hero-outline">📡 Track Shipment</a>
            @endauth
        </div>
    </div>
</section>

{{-- ── Section 2: Stats ─────────────────────────────────────────────── --}}
<div class="stats-strip">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item"><div class="stat-number">35+</div><div class="stat-label">Years of Trust</div></div>
            <div class="stat-item"><div class="stat-number">5L+</div><div class="stat-label">Shipments Delivered</div></div>
            <div class="stat-item"><div class="stat-number">500+</div><div class="stat-label">Cities Covered</div></div>
            <div class="stat-item"><div class="stat-number">98.2%</div><div class="stat-label">On-Time Delivery</div></div>
            <div class="stat-item"><div class="stat-number">10K+</div><div class="stat-label">Happy Clients</div></div>
        </div>
    </div>
</div>

{{-- ── Section 3: Why Choose Us ─────────────────────────────────────── --}}
<section>
    <div class="container text-center">
        <span class="section-label">Why CourierPortal</span>
        <h2 class="section-title">Everything You Need to Ship Smarter</h2>
        <p class="section-sub">Built for businesses that need reliability, transparency, and speed.</p>
        <div class="pillars-grid">
            <div class="pillar-card"><div class="pillar-icon">📦</div><div class="pillar-title">Transparent Pricing</div><div class="pillar-desc">No hidden charges. Our rate calculator shows you the exact cost before you book.</div></div>
            <div class="pillar-card"><div class="pillar-icon">📡</div><div class="pillar-title">Real-Time Tracking</div><div class="pillar-desc">Live shipment tracking with SMS and email notifications at every milestone.</div></div>
            <div class="pillar-card"><div class="pillar-icon">🤝</div><div class="pillar-title">Dedicated Support</div><div class="pillar-desc">A dedicated account manager for your business. Available 6 days a week.</div></div>
            <div class="pillar-card"><div class="pillar-icon">🔒</div><div class="pillar-title">Insured Shipments</div><div class="pillar-desc">Every shipment is covered with transit insurance up to declared value.</div></div>
            <div class="pillar-card"><div class="pillar-icon">🗺️</div><div class="pillar-title">Pan-India Network</div><div class="pillar-desc">Reach 500+ cities across India including tier-2 and tier-3 destinations.</div></div>
            <div class="pillar-card"><div class="pillar-icon">⚡</div><div class="pillar-title">Same-Day Pickup</div><div class="pillar-desc">Book before 2 PM and get same-day pickup in major metros.</div></div>
        </div>
    </div>
</section>

{{-- ── Section 4: How It Works ─────────────────────────────────────── --}}
<section class="how-bg">
    <div class="container text-center">
        <span class="section-label">Simple Process</span>
        <h2 class="section-title">Ship in 3 Easy Steps</h2>
        <p class="section-sub">From booking to delivery — we handle everything.</p>
        <div class="steps-grid">
            <div class="step-card">
                <div class="step-icon">📝</div>
                <div class="step-num">1</div>
                <div class="step-title">Book Online</div>
                <div class="step-desc">Fill our simple online form in under 2 minutes. Get instant rate quote.</div>
            </div>
            <div class="step-card">
                <div class="step-icon">🚚</div>
                <div class="step-num">2</div>
                <div class="step-title">We Pick Up</div>
                <div class="step-desc">Our courier partner collects the shipment from your address.</div>
            </div>
            <div class="step-card">
                <div class="step-icon">✅</div>
                <div class="step-num">3</div>
                <div class="step-title">Delivered</div>
                <div class="step-desc">Real-time tracking until it reaches the recipient's doorstep.</div>
            </div>
        </div>
    </div>
</section>

{{-- ── Section 5: Track Widget ─────────────────────────────────────── --}}
<section class="track-section">
    <div class="container text-center">
        <span class="section-label">Live Tracking</span>
        <h2 class="section-title">Track Your Shipment</h2>
        <p class="section-sub">Enter your AWB number to get real-time shipment status.</p>
        <div class="track-card">
            <div style="font-size:40px;margin-bottom:12px">📡</div>
            <p style="font-size:14px;color:#64748b;margin-bottom:0">Enter your AWB / Tracking Number</p>
            <form method="GET" id="track-form" onsubmit="trackShipment(event)">
                <div class="track-input-row">
                    <input type="text" id="awb-input" class="track-input" placeholder="e.g. CP20241701001" style="font-family:monospace;letter-spacing:1px">
                    <button type="submit" class="track-btn">🔍 Track Now</button>
                </div>
            </form>
        </div>
    </div>
</section>

{{-- ── Section 6: Testimonials ─────────────────────────────────────── --}}
<section class="testi-bg">
    <div class="container text-center">
        <span class="section-label">Client Stories</span>
        <h2 class="section-title">Trusted by 10,000+ Businesses</h2>
        <div class="testi-grid">
            <div class="testi-card">
                <div class="stars">★★★★★</div>
                <p class="testi-quote">"Seamless online booking and my parcels always arrive on time. Best courier service for our e-commerce business!"</p>
                <div class="testi-author">
                    <div class="testi-avatar">P</div>
                    <div><div class="testi-name">Priya Sharma</div><div class="testi-co">StyleCart · Delhi</div></div>
                </div>
            </div>
            <div class="testi-card">
                <div class="stars">★★★★★</div>
                <p class="testi-quote">"The rate calculator is transparent and accurate. No hidden charges ever. Highly recommended for all businesses!"</p>
                <div class="testi-author">
                    <div class="testi-avatar">R</div>
                    <div><div class="testi-name">Rajesh Kumar</div><div class="testi-co">TechSupplies Co. · Mumbai</div></div>
                </div>
            </div>
            <div class="testi-card">
                <div class="stars">★★★★★</div>
                <p class="testi-quote">"Customer support is quick and the tracking updates are real-time. Using CourierPortal for 3 years now."</p>
                <div class="testi-author">
                    <div class="testi-avatar">A</div>
                    <div><div class="testi-name">Anita Mehta</div><div class="testi-co">Handcraft India · Jaipur</div></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ── Section 7: Contact / Map ─────────────────────────────────────── --}}
<section>
    <div class="container text-center">
        <span class="section-label">Find Us</span>
        <h2 class="section-title">Get in Touch</h2>
        <div class="contact-grid">
            <div class="contact-card" style="text-align:left">
                <div class="contact-item">
                    <div class="contact-icon">📞</div>
                    <div><div class="contact-label">Phone</div><div class="contact-value">+91-98765-43210</div></div>
                </div>
                <div class="contact-item">
                    <div class="contact-icon">📧</div>
                    <div><div class="contact-label">Email</div><div class="contact-value">info@courierportal.com</div></div>
                </div>
                <div class="contact-item">
                    <div class="contact-icon">📍</div>
                    <div><div class="contact-label">Address</div><div class="contact-value">123, Logistics Hub, Industrial Area,<br>Your City – 110001</div></div>
                </div>
                <div class="contact-item">
                    <div class="contact-icon">🕐</div>
                    <div><div class="contact-label">Business Hours</div><div class="contact-value">Mon–Sat: 9AM – 6PM</div></div>
                </div>
                <a href="https://wa.me/919876543210" style="display:inline-flex;align-items:center;gap:8px;background:#25d366;color:#fff;padding:10px 20px;border-radius:9px;font-weight:600;font-size:13.5px;text-decoration:none;margin-top:8px">
                    💬 Chat on WhatsApp
                </a>
            </div>
            <div class="map-placeholder">
                <div style="font-size:40px">🗺️</div>
                <h3 style="font-size:16px;font-weight:600;color:#475569;margin-top:12px">Google Maps</h3>
                <p>Add your Google Maps embed URL here.<br><code style="font-size:12px;background:#e2e8f0;padding:3px 8px;border-radius:4px">&lt;iframe src="EMBED_URL"&gt;</code></p>
            </div>
        </div>
    </div>
</section>

{{-- ── Section 8: FAQ ───────────────────────────────────────────────── --}}
<section class="how-bg" id="faq">
    <div class="container text-center">
        <span class="section-label">Got Questions?</span>
        <h2 class="section-title">Frequently Asked Questions</h2>
        <div class="faq-list">
            @php $faqs = [
                ['How do I book a shipment?', "Register for a free account, log in, and use our simple booking form. Fill in sender and receiver details, select a service, and confirm. You'll receive an AWB number instantly."],
                ['What is the minimum weight for a shipment?', 'The minimum chargeable weight is 500 grams (0.5 kg). Dimensional weight may apply for larger, lighter packages.'],
                ['How can I track my shipment?', 'Use the Track Shipment section on our homepage or your dashboard. Enter your AWB number to get real-time status updates.'],
                ['What are your delivery timeframes?', 'Express Air: 1-2 business days. Priority Surface: 3-5 days. Economy Surface: 5-8 days. Timeframes may vary for ODA (Out of Delivery Area) locations.'],
                ['Do you offer pickup service?', 'Yes! We offer free doorstep pickup for all bookings. Book before 2 PM for same-day pickup in major metros.'],
                ['How is the shipping rate calculated?', 'Rates are based on weight (actual or volumetric, whichever is higher), service type, and delivery zone. Use our Rate Calculator for an instant quote.'],
                ['Are shipments insured?', 'Yes, every shipment carries basic transit insurance. You can declare the value at the time of booking for enhanced coverage.'],
                ['What items are prohibited?', 'We do not ship hazardous materials, explosives, perishable food, currency, or items banned by Indian law. See our full prohibited items list.'],
                ['How do I get an invoice for my shipments?', 'Invoices are generated automatically and available in your dashboard under My Bookings. You can download PDF invoices for each shipment.'],
                ['Can I cancel or modify a booking?', 'You can cancel a booking before it is picked up. Once picked up, modifications are not possible. Contact support for urgent changes.'],
            ]; @endphp

            @foreach($faqs as $i => $faq)
            <div class="faq-item" x-data="{ open: false }">
                <button class="faq-trigger" @click="open = !open">
                    <span>{{ $faq[0] }}</span>
                    <svg class="faq-chevron" :style="open ? 'transform:rotate(180deg)' : ''" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
                <div class="faq-body" x-show="open" x-collapse style="display:none">{{ $faq[1] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ── Section 9: CTA Banner ────────────────────────────────────────── --}}
<section class="cta-banner">
    <div class="container">
        <h2>Ready to Ship Smarter?</h2>
        <p>Create your free account today and get instant access to all features.</p>
        <div class="cta-btns">
            <a href="{{ route('client.register') }}" class="btn-cta-white">✨ Register Now — Free</a>
            <a href="{{ route('tracking.public') }}" class="btn-cta-outline">📡 Track a Shipment</a>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
function trackShipment(e) {
    e.preventDefault();
    const awb = document.getElementById('awb-input').value.trim();
    if (awb) window.location.href = '/track/' + encodeURIComponent(awb);
}
</script>
@endpush
