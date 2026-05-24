<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('page-title', 'Admin') — CourierPortal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        /* ── CSS Custom Properties ─────────────────────────────────── */
        :root {
            --sidebar-w: 260px;
            --sidebar-bg: #0f172a;
            --sidebar-surface: #1e293b;
            --sidebar-border: #334155;
            --sidebar-text: #94a3b8;
            --sidebar-text-active: #f1f5f9;
            --sidebar-accent: #3b82f6;
            --sidebar-accent-bg: rgba(59,130,246,0.15);
            --topbar-h: 64px;
            --topbar-bg: #ffffff;
            --body-bg: #f1f5f9;
            --card-bg: #ffffff;
        }

        /* ── Reset / Base ──────────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: var(--body-bg);
            color: #1e293b;
            min-height: 100vh;
        }

        /* ── Sidebar ───────────────────────────────────────────────── */
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            z-index: 50;
            border-right: 1px solid var(--sidebar-border);
            overflow: hidden;
        }

        /* Brand */
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 20px 20px 18px;
            border-bottom: 1px solid var(--sidebar-border);
            text-decoration: none;
        }
        .sidebar-brand-icon {
            width: 38px; height: 38px;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }
        .sidebar-brand-text { line-height: 1; }
        .sidebar-brand-name {
            font-size: 15px;
            font-weight: 700;
            color: #f1f5f9;
            letter-spacing: 0.02em;
        }
        .sidebar-brand-sub {
            font-size: 10px;
            color: var(--sidebar-text);
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin-top: 2px;
        }

        /* Nav */
        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 12px 0;
            scrollbar-width: thin;
            scrollbar-color: #334155 transparent;
        }
        .sidebar-section-label {
            font-size: 10px;
            font-weight: 600;
            color: #475569;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            padding: 10px 20px 4px;
            margin-top: 4px;
        }
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 20px;
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            border-radius: 0;
            transition: all 0.15s ease;
            position: relative;
            margin: 1px 10px;
            border-radius: 8px;
        }
        .sidebar-link:hover {
            color: var(--sidebar-text-active);
            background: rgba(255,255,255,0.05);
        }
        .sidebar-link.active {
            color: var(--sidebar-text-active);
            background: var(--sidebar-accent-bg);
        }
        .sidebar-link.active::before {
            content: '';
            position: absolute;
            left: -10px; top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 60%;
            background: var(--sidebar-accent);
            border-radius: 0 2px 2px 0;
        }
        .sidebar-link .nav-icon {
            width: 18px; height: 18px;
            flex-shrink: 0;
            opacity: 0.7;
            transition: opacity 0.15s;
        }
        .sidebar-link.active .nav-icon,
        .sidebar-link:hover .nav-icon { opacity: 1; }
        .nav-badge {
            margin-left: auto;
            font-size: 10px;
            background: rgba(59,130,246,0.2);
            color: #60a5fa;
            padding: 2px 7px;
            border-radius: 20px;
            font-weight: 600;
        }

        /* Sidebar footer */
        .sidebar-footer {
            padding: 14px 16px;
            border-top: 1px solid var(--sidebar-border);
            background: var(--sidebar-surface);
        }
        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }
        .sidebar-avatar {
            width: 34px; height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            display: flex; align-items: center; justify-content: center;
            font-size: 13px;
            font-weight: 700;
            color: white;
            flex-shrink: 0;
        }
        .sidebar-user-info { overflow: hidden; }
        .sidebar-user-name {
            font-size: 13px;
            font-weight: 600;
            color: #e2e8f0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .sidebar-user-role {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .btn-logout {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 8px 12px;
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.2);
            color: #f87171;
            font-size: 12.5px;
            font-weight: 500;
            border-radius: 7px;
            cursor: pointer;
            transition: all 0.15s;
            text-decoration: none;
        }
        .btn-logout:hover {
            background: rgba(239,68,68,0.18);
            color: #fca5a5;
        }

        /* ── Main wrapper ──────────────────────────────────────────── */
        .main-wrapper {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── Topbar ────────────────────────────────────────────────── */
        .topbar {
            position: sticky;
            top: 0;
            height: var(--topbar-h);
            background: var(--topbar-bg);
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            z-index: 40;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        }
        .topbar-title {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .topbar-right {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .topbar-admin-name {
            font-size: 13.5px;
            font-weight: 600;
            color: #334155;
        }
        .badge-admin {
            background: #eff6ff;
            color: #2563eb;
            border: 1px solid #bfdbfe;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.08em;
            padding: 3px 9px;
            border-radius: 20px;
        }

        /* ── Flash alerts ──────────────────────────────────────────── */
        .flash-wrap { padding: 10px 28px 0; }
        .flash {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 500;
            animation: slideDown 0.25s ease;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .flash-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #15803d;
        }
        .flash-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
        }

        /* ── Content ───────────────────────────────────────────────── */
        .content-area {
            flex: 1;
            padding: 24px 28px 40px;
        }

        /* ── Utility card ──────────────────────────────────────────── */
        .card {
            background: var(--card-bg);
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- ═══════════════════════════════════════════════════════ SIDEBAR ══ --}}
<aside class="sidebar">
    {{-- Brand --}}
    <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
        <div class="sidebar-brand-icon">🚚</div>
        <div class="sidebar-brand-text">
            <div class="sidebar-brand-name">CourierPortal</div>
            <div class="sidebar-brand-sub">Admin Panel</div>
        </div>
    </a>

    {{-- Navigation --}}
    <nav class="sidebar-nav">
        <div class="sidebar-section-label">Main Menu</div>

        <a href="{{ route('admin.dashboard') }}"
           class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7" rx="1"/>
                <rect x="14" y="3" width="7" height="7" rx="1"/>
                <rect x="3" y="14" width="7" height="7" rx="1"/>
                <rect x="14" y="14" width="7" height="7" rx="1"/>
            </svg>
            Dashboard
        </a>

        <a href="{{ route('admin.bookings.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.bookings*') ? 'active' : '' }}">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M5 12h14M5 12l4-4m-4 4 4 4"/>
                <path d="M3 6h18M3 18h18"/>
            </svg>
            Bookings
        </a>

        <a href="{{ route('admin.clients.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.clients.*') ? 'active' : '' }}">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="9" cy="7" r="4"/>
                <path d="M3 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                <path d="M21 21v-2a4 4 0 0 0-3-3.85"/>
            </svg>
            Clients
        </a>

        <a href="{{ route('admin.rates.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.rates.*') ? 'active' : '' }}">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 7H6a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3"/>
                <rect x="9" y="3" width="6" height="6" rx="1"/>
                <path d="M9 12h6M9 16h4"/>
            </svg>
            Rate Cards
        </a>

        <div class="sidebar-section-label" style="margin-top:8px">Operations</div>

        <a href="{{ route('admin.reports.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.reports*') ? 'active' : '' }}">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 20V10M12 20V4M6 20v-6"/>
            </svg>
            Reports
        </a>

        <a href="{{ route('admin.tickets.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.tickets*') ? 'active' : '' }}">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>
            Support Tickets
        </a>

        <a href="{{ route('admin.settings.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
            <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="3"/>
                <path d="M19.07 4.93a10 10 0 0 0-14.14 0M4.93 19.07a10 10 0 0 0 14.14 0"/>
                <path d="M12 2v2M12 20v2M2 12h2M20 12h2"/>
            </svg>
            Settings
        </a>
    </nav>

    {{-- Footer: user info + logout --}}
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-avatar">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name">{{ Auth::user()->name }}</div>
                <div class="sidebar-user-role">Administrator</div>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="btn-logout">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
                Sign Out
            </button>
        </form>
    </div>
</aside>

{{-- ══════════════════════════════════════════════════ MAIN WRAPPER ══ --}}
<div class="main-wrapper">

    {{-- Top navigation bar --}}
    <header class="topbar">
        <div class="topbar-title">
            @yield('page-title', 'Admin Panel')
        </div>
        <div class="topbar-right">
            <span class="topbar-admin-name">{{ Auth::user()->name }}</span>
            <span class="badge-admin">ADMIN</span>
        </div>
    </header>

    {{-- Flash messages --}}
    @if(session('success') || session('error') || $errors->any())
    <div class="flash-wrap">
        @if(session('success'))
            <div class="flash flash-success">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-top:1px;flex-shrink:0">
                    <circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="flash flash-error">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-top:1px;flex-shrink:0">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><circle cx="12" cy="16" r="0.5" fill="currentColor"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif
        @if($errors->any())
            <div class="flash flash-error">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-top:1px;flex-shrink:0">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><circle cx="12" cy="16" r="0.5" fill="currentColor"/>
                </svg>
                <div>
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
    @endif

    {{-- Page content --}}
    <main class="content-area">
        @yield('content')
    </main>

</div>

@stack('scripts')
</body>
</html>
