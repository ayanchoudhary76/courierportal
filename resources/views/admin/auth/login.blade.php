<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Login — CourierPortal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', system-ui, sans-serif;
            padding: 20px;
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
        }

        /* Brand header above card */
        .login-brand {
            text-align: center;
            margin-bottom: 24px;
        }
        .login-brand-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            border-radius: 16px;
            font-size: 26px;
            margin-bottom: 12px;
            box-shadow: 0 8px 25px rgba(59,130,246,0.4);
        }
        .login-brand-name {
            font-size: 22px;
            font-weight: 800;
            color: #f1f5f9;
            letter-spacing: -0.02em;
        }
        .login-brand-sub {
            font-size: 12px;
            color: #64748b;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin-top: 3px;
        }

        /* Card */
        .login-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 36px 36px 32px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.35), 0 0 0 1px rgba(255,255,255,0.05);
        }
        .login-card-title {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
        }
        .login-card-sub {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 26px;
        }

        /* Alert */
        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 11px 14px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 20px;
            display: flex;
            gap: 8px;
            align-items: flex-start;
        }

        /* Form */
        .form-group {
            margin-bottom: 18px;
        }
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }
        .form-input {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            color: #0f172a;
            background: #f8fafc;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }
        .form-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.12);
            background: #ffffff;
        }
        .form-input.is-invalid {
            border-color: #ef4444;
        }
        .form-error {
            font-size: 12px;
            color: #ef4444;
            margin-top: 5px;
            font-weight: 500;
        }

        /* Password toggle wrapper */
        .password-wrap {
            position: relative;
        }
        .password-wrap .form-input {
            padding-right: 44px;
        }
        .pw-toggle {
            position: absolute;
            right: 13px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #94a3b8;
            padding: 4px;
            display: flex;
            align-items: center;
            transition: color 0.15s;
        }
        .pw-toggle:hover { color: #3b82f6; }

        /* Submit */
        .btn-submit {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #2563eb, #4f46e5);
            color: #ffffff;
            font-size: 14.5px;
            font-weight: 700;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            letter-spacing: 0.02em;
            transition: opacity 0.2s, transform 0.1s;
            margin-top: 6px;
            box-shadow: 0 4px 14px rgba(37,99,235,0.4);
        }
        .btn-submit:hover { opacity: 0.92; }
        .btn-submit:active { transform: scale(0.99); }

        /* Footer note */
        .login-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #64748b;
        }
    </style>
</head>
<body>
<div class="login-wrapper">

    {{-- Brand --}}
    <div class="login-brand">
        <div class="login-brand-icon">🚚</div>
        <div class="login-brand-name">CourierPortal</div>
        <div class="login-brand-sub">Admin Panel</div>
    </div>

    {{-- Card --}}
    <div class="login-card">
        <div class="login-card-title">Welcome back</div>
        <div class="login-card-sub">Sign in to your admin account</div>

        {{-- Session error alert --}}
        @if($errors->has('email') && !$errors->has('email', 'default'))
        @endif
        @if(session('success'))
            <div class="alert-error" style="background:#f0fdf4;border-color:#bbf7d0;color:#15803d">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0;margin-top:1px"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="alert-error">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0;margin-top:1px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><circle cx="12" cy="16" r="0.5" fill="currentColor"/></svg>
                <div>
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.post') }}" id="loginForm">
            @csrf

            {{-- Email --}}
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                    value="{{ old('email') }}"
                    placeholder="admin@courierportal.com"
                    autocomplete="email"
                    autofocus
                    required
                >
            </div>

            {{-- Password --}}
            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <div class="password-wrap">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                        placeholder="Enter your password"
                        autocomplete="current-password"
                        required
                    >
                    <button type="button" class="pw-toggle" id="pwToggle" title="Toggle password visibility">
                        <svg id="eyeIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn-submit" id="submitBtn">
                Sign In
            </button>
        </form>
    </div>

    <div class="login-footer">
        &copy; {{ date('Y') }} CourierPortal. All rights reserved.
    </div>
</div>

<script>
// Password show/hide toggle
const pwToggle = document.getElementById('pwToggle');
const pwInput  = document.getElementById('password');
const eyeIcon  = document.getElementById('eyeIcon');

const eyeOpen = `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>`;
const eyeOff  = `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/>`;

pwToggle.addEventListener('click', () => {
    const isHidden = pwInput.type === 'password';
    pwInput.type   = isHidden ? 'text' : 'password';
    eyeIcon.innerHTML = isHidden ? eyeOff : eyeOpen;
});

// Disable button while submitting
document.getElementById('loginForm').addEventListener('submit', function () {
    const btn = document.getElementById('submitBtn');
    btn.textContent = 'Signing in…';
    btn.disabled = true;
});
</script>
</body>
</html>
