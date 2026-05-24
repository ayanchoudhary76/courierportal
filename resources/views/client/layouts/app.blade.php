<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('page-title', 'CourierPortal') — Fast & Reliable Shipping</title>
    <meta name="description" content="@yield('meta-description', 'CourierPortal — Pan-India and International shipping with real-time tracking, transparent pricing, and 35+ years of trust.')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        /* ── Base ──────────────────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', system-ui, sans-serif; color: #1e293b; background: #fff; line-height: 1.6; }

        /* ── Navbar ────────────────────────────────────────────────── */
        .navbar {
            position: sticky; top: 0; z-index: 100;
            background: #fff; border-bottom: 1px solid #f1f5f9;
            box-shadow: 0 1px 6px rgba(0,0,0,0.06);
        }
        .nav-inner {
            max-width: 1200px; margin: 0 auto;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 24px; height: 64px;
        }
        .nav-brand {
            display: flex; align-items: center; gap: 9px;
            text-decoration: none; font-size: 18px; font-weight: 800;
            color: #1e293b; flex-shrink: 0;
        }
        .nav-brand-icon {
            width: 36px; height: 36px; border-radius: 10px;
            background: linear-gradient(135deg, #2563eb, #4f46e5);
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
        }
        .nav-brand span { color: #2563eb; }
        .nav-links { display: flex; align-items: center; gap: 4px; }
        .nav-link {
            padding: 7px 14px; border-radius: 8px; text-decoration: none;
            font-size: 13.5px; font-weight: 500; color: #475569;
            transition: all 0.15s;
        }
        .nav-link:hover { color: #1e293b; background: #f1f5f9; }
        .nav-link.active { color: #2563eb; background: #eff6ff; }
        .nav-actions { display: flex; align-items: center; gap: 8px; }
        .btn-nav {
            padding: 7px 16px; border-radius: 8px; font-size: 13.5px; font-weight: 600;
            text-decoration: none; border: none; cursor: pointer; transition: all 0.15s;
        }
        .btn-nav-outline { border: 1.5px solid #e2e8f0; color: #475569; background: #fff; }
        .btn-nav-outline:hover { border-color: #2563eb; color: #2563eb; }
        .btn-nav-solid { background: #2563eb; color: #fff; }
        .btn-nav-solid:hover { background: #1d4ed8; }

        /* Dropdown */
        .nav-dropdown { position: relative; }
        .dropdown-trigger {
            display: flex; align-items: center; gap: 6px;
            padding: 6px 12px; border-radius: 8px; cursor: pointer;
            font-size: 13.5px; font-weight: 500; color: #475569; border: none; background: transparent;
        }
        .dropdown-trigger:hover { background: #f1f5f9; color: #1e293b; }
        .dropdown-menu {
            position: absolute; right: 0; top: calc(100% + 8px);
            background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
            min-width: 180px; box-shadow: 0 8px 24px rgba(0,0,0,0.12);
            padding: 6px;
        }
        .dropdown-item {
            display: flex; align-items: center; gap: 8px;
            padding: 9px 12px; border-radius: 8px; text-decoration: none;
            font-size: 13.5px; color: #475569; transition: all 0.1s;
        }
        .dropdown-item:hover { background: #f8fafc; color: #1e293b; }
        .dropdown-divider { border: none; border-top: 1px solid #f1f5f9; margin: 4px 0; }

        /* Mobile menu */
        .hamburger {
            display: none; flex-direction: column; gap: 4px;
            cursor: pointer; padding: 6px; border: none; background: none;
        }
        .hamburger span { display: block; width: 22px; height: 2px; background: #475569; border-radius: 2px; transition: 0.2s; }
        .mobile-menu {
            background: #fff; border-top: 1px solid #f1f5f9;
            padding: 12px 24px 20px;
        }
        .mobile-link {
            display: block; padding: 10px 12px; border-radius: 8px;
            text-decoration: none; font-size: 14px; font-weight: 500; color: #475569;
        }
        .mobile-link:hover { background: #f1f5f9; color: #1e293b; }

        @media (max-width: 768px) {
            .nav-links, .nav-actions { display: none; }
            .hamburger { display: flex; }
        }

        /* ── Flash messages ─────────────────────────────────────────── */
        .flash-container { max-width: 1200px; margin: 0 auto; padding: 10px 24px 0; }
        .flash {
            display: flex; align-items: flex-start; gap: 10px;
            padding: 12px 16px; border-radius: 8px;
            font-size: 13.5px; font-weight: 500;
            animation: slideDown 0.25s ease;
        }
        @keyframes slideDown { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:translateY(0); } }
        .flash-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }
        .flash-error   { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; }
        .flash-info    { background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; }

        /* ── Main content ────────────────────────────────────────────── */
        main { min-height: calc(100vh - 64px - 300px); }

        /* ── Footer ─────────────────────────────────────────────────── */
        footer { background: #1e293b; color: #94a3b8; }
        .footer-grid {
            max-width: 1200px; margin: 0 auto;
            display: grid; grid-template-columns: 2fr 1fr 1fr 1.5fr;
            gap: 40px; padding: 56px 24px 40px;
        }
        @media (max-width: 900px) { .footer-grid { grid-template-columns: 1fr 1fr; } }
        @media (max-width: 500px) { .footer-grid { grid-template-columns: 1fr; } }
        .footer-brand-name { font-size: 18px; font-weight: 800; color: #f1f5f9; margin-bottom: 8px; display: flex; align-items: center; gap: 8px; }
        .footer-tagline { font-size: 13px; color: #64748b; line-height: 1.5; margin-bottom: 12px; }
        .footer-gst { font-size: 11.5px; color: #475569; font-family: monospace; }
        .footer-col-title { font-size: 11px; font-weight: 700; color: #f1f5f9; letter-spacing: 0.1em; text-transform: uppercase; margin-bottom: 14px; }
        .footer-link { display: block; font-size: 13.5px; color: #64748b; text-decoration: none; padding: 3px 0; transition: color 0.15s; }
        .footer-link:hover { color: #94a3b8; }
        .footer-contact-item { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #64748b; margin-bottom: 10px; }
        .footer-bottom {
            border-top: 1px solid #334155; max-width: 1200px; margin: 0 auto;
            padding: 18px 24px; display: flex; justify-content: space-between;
            align-items: center; font-size: 12.5px; flex-wrap: wrap; gap: 8px;
        }
        .footer-bottom-links { display: flex; gap: 20px; }
        .footer-bottom-links a { color: #475569; text-decoration: none; }
        .footer-bottom-links a:hover { color: #64748b; }
        .whatsapp-btn {
            display: inline-flex; align-items: center; gap: 6px;
            background: #25d366; color: #fff; padding: 7px 14px;
            border-radius: 8px; font-size: 12.5px; font-weight: 600;
            text-decoration: none; margin-top: 6px; transition: opacity 0.15s;
        }
        .whatsapp-btn:hover { opacity: 0.9; }
    </style>
    @stack('styles')
</head>
<body>

{{-- ══════════════════════════════════════════════════════ NAVBAR ══ --}}
<nav class="navbar" x-data="{ mobileOpen: false, dropdownOpen: false }">
    <div class="nav-inner">
        {{-- Brand --}}
        <a href="{{ route('home') }}" class="nav-brand">
            <div class="nav-brand-icon">🚚</div>
            <span>Courier<span>Portal</span></span>
        </a>

        {{-- Desktop nav links --}}
        <div class="nav-links">
            <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
            <a href="{{ route('tracking.public') }}" class="nav-link {{ request()->routeIs('tracking.public') ? 'active' : '' }}">Track Shipment</a>
            @auth
            <a href="{{ route('client.rates') }}" class="nav-link {{ request()->routeIs('client.rates') ? 'active' : '' }}">Rate Calculator</a>
            <a href="{{ route('client.book') }}" class="nav-link {{ request()->routeIs('client.book') ? 'active' : '' }}">Book Now</a>
            @else
            <a href="{{ route('client.login') }}#rates" class="nav-link">Rate Calculator</a>
            <a href="{{ route('client.register') }}" class="nav-link">Book Now</a>
            @endauth
        </div>

        {{-- Desktop actions --}}
        <div class="nav-actions">
            @auth
            <div class="nav-dropdown" x-data="{ open: false }" @click.away="open = false">
                <button class="dropdown-trigger" @click="open = !open">
                    <div style="width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,#2563eb,#4f46e5);color:#fff;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    Hi, {{ Str::words(Auth::user()->name, 1, '') }}
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
                <div class="dropdown-menu" x-show="open" x-cloak style="display:none">
                    <a href="{{ route('client.dashboard') }}" class="dropdown-item">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                        Dashboard
                    </a>
                    <a href="{{ route('client.profile') }}" class="dropdown-item">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        Profile
                    </a>
                    <a href="{{ route('client.bookings') }}" class="dropdown-item">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M5 12l4-4m-4 4 4 4"/><path d="M3 6h18M3 18h18"/></svg>
                        My Bookings
                    </a>
                    <hr class="dropdown-divider">
                    <form method="POST" action="{{ route('client.logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item" style="width:100%;border:none;cursor:pointer;background:none;text-align:left;color:#dc2626">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>
            @else
            <a href="{{ route('client.login') }}" class="btn-nav btn-nav-outline">Login</a>
            <a href="{{ route('client.register') }}" class="btn-nav btn-nav-solid">Register Free</a>
            @endauth
        </div>

        {{-- Hamburger --}}
        <button class="hamburger" @click="mobileOpen = !mobileOpen" aria-label="Menu">
            <span></span><span></span><span></span>
        </button>
    </div>

    {{-- Mobile menu --}}
    <div class="mobile-menu" x-show="mobileOpen" x-cloak style="display:none">
        <a href="{{ route('home') }}" class="mobile-link">🏠 Home</a>
        <a href="{{ route('tracking.public') }}" class="mobile-link">📡 Track Shipment</a>
        @auth
        <a href="{{ route('client.rates') }}" class="mobile-link">💰 Rate Calculator</a>
        <a href="{{ route('client.book') }}" class="mobile-link">📦 Book Now</a>
        <a href="{{ route('client.dashboard') }}" class="mobile-link">📊 Dashboard</a>
        <a href="{{ route('client.profile') }}" class="mobile-link">👤 Profile</a>
        <form method="POST" action="{{ route('client.logout') }}">
            @csrf
            <button type="submit" class="mobile-link" style="width:100%;border:none;background:none;cursor:pointer;color:#dc2626;text-align:left">🚪 Sign Out</button>
        </form>
        @else
        <a href="{{ route('client.login') }}" class="mobile-link">🔐 Login</a>
        <a href="{{ route('client.register') }}" class="mobile-link" style="color:#2563eb;font-weight:600">✨ Register Free</a>
        @endauth
    </div>
</nav>

{{-- Flash messages --}}
@if(session('success') || session('error') || session('info') || session('status') || $errors->any())
<div class="flash-container">
    @if(session('success'))
        <div class="flash flash-success">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0;margin-top:1px"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="flash flash-error">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0;margin-top:1px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/></svg>
            {{ session('error') }}
        </div>
    @endif
    @if(session('info') || session('status'))
        <div class="flash flash-info">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0;margin-top:1px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><circle cx="12" cy="8" r="0.5" fill="currentColor"/></svg>
            {{ session('info') ?? session('status') }}
        </div>
    @endif
    @if($errors->any())
        <div class="flash flash-error">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0;margin-top:1px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/></svg>
            <div>@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
        </div>
    @endif
</div>
@endif

{{-- Page content --}}
<main>@yield('content')</main>

{{-- ═══════════════════════════════════════════════════════ FOOTER ══ --}}
<footer>
    <div class="footer-grid">
        {{-- Company --}}
        <div>
            <div class="footer-brand-name">🚚 CourierPortal</div>
            <p class="footer-tagline">Delivering Trust Since 1989. Pan-India & International shipping with real-time tracking and transparent pricing.</p>
            <p class="footer-gst">GSTIN: 22AAAAA0000A1Z5</p>
        </div>
        {{-- Quick Links --}}
        <div>
            <div class="footer-col-title">Quick Links</div>
            <a href="{{ route('home') }}" class="footer-link">Home</a>
            <a href="{{ route('tracking.public') }}" class="footer-link">Track Shipment</a>
            <a href="{{ route('client.login') }}" class="footer-link">Rate Calculator</a>
            <a href="{{ route('client.register') }}" class="footer-link">Book Now</a>
            <a href="{{ route('home') }}#faq" class="footer-link">FAQ</a>
        </div>
        {{-- Services --}}
        <div>
            <div class="footer-col-title">Services</div>
            <span class="footer-link">✈️ Express Air</span>
            <span class="footer-link">🚛 Priority Surface</span>
            <span class="footer-link">📦 Economy Surface</span>
            <span class="footer-link">🌍 International</span>
        </div>
        {{-- Contact --}}
        <div>
            <div class="footer-col-title">Contact Us</div>
            <div class="footer-contact-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13.5 19.79 19.79 0 0 1 1.61 4.93 2 2 0 0 1 3.58 2.73h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6.06 6.06l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                +91-98765-43210
            </div>
            <div class="footer-contact-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                info@courierportal.com
            </div>
            <a href="https://wa.me/919876543210" class="whatsapp-btn" target="_blank">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>
                WhatsApp Us
            </a>
        </div>
    </div>
    <div style="max-width:1200px;margin:0 auto;padding:0 24px">
        <div class="footer-bottom">
            <span>© {{ date('Y') }} CourierPortal. All rights reserved.</span>
            <div class="footer-bottom-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">Refund Policy</a>
            </div>
        </div>
    </div>
</footer>

@stack('scripts')
</body>
</html>
