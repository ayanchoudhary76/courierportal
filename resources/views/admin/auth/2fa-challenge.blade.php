<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Authentication — CourierPortal Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background: #0f172a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        body::before {
            content: '';
            position: fixed; inset: 0;
            background: radial-gradient(ellipse at 20% 50%, rgba(59,130,246,0.08) 0%, transparent 60%),
                        radial-gradient(ellipse at 80% 20%, rgba(139,92,246,0.06) 0%, transparent 50%);
            pointer-events: none;
        }
        .card {
            background: #1e293b;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.5);
            text-align: center;
        }
        .lock-icon {
            width: 70px; height: 70px;
            background: linear-gradient(135deg, #1d4ed8, #4f46e5);
            border-radius: 20px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 8px 24px rgba(37,99,235,0.35);
        }
        .lock-icon svg { width: 36px; height: 36px; color: #fff; }
        h1 {
            font-size: 22px;
            font-weight: 800;
            color: #f1f5f9;
            margin-bottom: 8px;
        }
        .subtitle {
            font-size: 13px;
            color: #94a3b8;
            line-height: 1.5;
            margin-bottom: 28px;
        }
        .error-msg {
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.3);
            border-radius: 8px;
            padding: 10px 14px;
            color: #fca5a5;
            font-size: 13px;
            margin-bottom: 14px;
            text-align: left;
        }
        .otp-label {
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 10px;
            text-align: left;
        }
        .otp-input {
            width: 100%;
            padding: 16px;
            background: rgba(255,255,255,0.05);
            border: 1.5px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            color: #f1f5f9;
            font-size: 32px;
            font-weight: 700;
            font-family: 'Courier New', monospace;
            text-align: center;
            letter-spacing: 0.5em;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
            margin-bottom: 16px;
        }
        .otp-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59,130,246,0.2);
        }
        .otp-input::placeholder { color: #1e3a5f; letter-spacing: 0.3em; }
        .btn-verify {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: opacity 0.15s, transform 0.1s;
            margin-bottom: 16px;
            letter-spacing: 0.02em;
        }
        .btn-verify:hover { opacity: 0.9; transform: translateY(-1px); }
        .back-link {
            font-size: 12.5px;
            color: #475569;
            text-decoration: none;
            display: block;
            transition: color 0.15s;
        }
        .back-link:hover { color: #94a3b8; }
        .dots-row {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-bottom: 20px;
        }
        .dot {
            width: 10px; height: 10px;
            border-radius: 50%;
            background: rgba(255,255,255,0.12);
            transition: background 0.15s;
        }
        .dot.filled { background: #3b82f6; }
    </style>
</head>
<body>
<div class="card"
     x-data="{
        otp: '',
        get filled() { return this.otp.length; },
        submit(form) { if (this.otp.length === 6) form.submit(); }
     }">

    <div class="lock-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
        </svg>
    </div>

    <h1>Two-Factor Authentication</h1>
    <p class="subtitle">
        Open <strong style="color:#f1f5f9;">Google Authenticator</strong> and enter the
        6-digit code for <strong style="color:#f1f5f9;">CourierPortal</strong>
    </p>

    {{-- Digit indicator dots --}}
    <div class="dots-row">
        <template x-for="i in 6" :key="i">
            <div class="dot" :class="{ filled: i <= filled }"></div>
        </template>
    </div>

    <form method="POST" action="{{ route('admin.2fa.verify') }}" x-ref="form">
        @csrf

        @if($errors->any())
            <div class="error-msg">{{ $errors->first() }}</div>
        @endif

        <div class="otp-label">Authenticator Code</div>
        <input type="text"
               name="one_time_password"
               class="otp-input"
               maxlength="6"
               pattern="\d{6}"
               inputmode="numeric"
               autocomplete="one-time-code"
               autofocus
               placeholder="······"
               x-model="otp"
               @input="submit($refs.form)"
               value="{{ old('one_time_password') }}">

        <button type="submit" class="btn-verify">
            Verify &amp; Sign In
        </button>
    </form>

    <a href="{{ route('admin.login') }}" class="back-link">
        &larr; Wrong account? Back to login
    </a>
</div>
</body>
</html>
