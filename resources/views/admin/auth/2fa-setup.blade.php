<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Up Two-Factor Authentication — CourierPortal Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
            max-width: 460px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.5);
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 28px;
        }
        .brand-icon {
            width: 44px; height: 44px;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px;
        }
        .brand-name { font-size: 18px; font-weight: 800; color: #f1f5f9; }
        .brand-sub  { font-size: 11px; color: #64748b; text-transform: uppercase; letter-spacing: 0.1em; }

        .step-list { list-style: none; counter-reset: steps; }
        .step-list li {
            counter-increment: steps;
            display: flex;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 22px;
        }
        .step-list li::before {
            content: counter(steps);
            flex-shrink: 0;
            width: 28px; height: 28px;
            background: #2563eb;
            color: #fff;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 700;
            margin-top: 2px;
        }
        .step-content h3 {
            font-size: 13.5px;
            font-weight: 700;
            color: #f1f5f9;
            margin-bottom: 4px;
        }
        .step-content p {
            font-size: 12px;
            color: #94a3b8;
            line-height: 1.5;
        }
        .store-links {
            display: flex;
            gap: 8px;
            margin-top: 6px;
        }
        .store-link {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 12px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            color: #cbd5e1;
            text-decoration: none;
            transition: background 0.15s;
        }
        .store-link:hover { background: rgba(255,255,255,0.1); }

        .qr-wrap {
            display: flex;
            justify-content: center;
            margin: 8px 0;
        }
        .qr-wrap svg {
            border-radius: 8px;
        }

        .divider {
            border: none;
            border-top: 1px solid rgba(255,255,255,0.07);
            margin: 20px 0;
        }

        .form-group { margin-bottom: 16px; }
        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 8px;
        }
        .otp-input {
            width: 100%;
            padding: 14px;
            background: rgba(255,255,255,0.05);
            border: 1.5px solid rgba(255,255,255,0.1);
            border-radius: 10px;
            color: #f1f5f9;
            font-size: 28px;
            font-weight: 700;
            font-family: 'Courier New', monospace;
            text-align: center;
            letter-spacing: 0.4em;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .otp-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.2);
        }
        .otp-input::placeholder { color: #334155; letter-spacing: 0.2em; }

        .error-msg {
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.3);
            border-radius: 8px;
            padding: 10px 14px;
            color: #fca5a5;
            font-size: 13px;
            margin-bottom: 14px;
        }
        .btn-enable {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #16a34a, #15803d);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: opacity 0.15s, transform 0.1s;
            margin-bottom: 12px;
        }
        .btn-enable:hover { opacity: 0.92; transform: translateY(-1px); }
        .skip-link {
            display: block;
            text-align: center;
            font-size: 12.5px;
            color: #64748b;
            text-decoration: none;
            transition: color 0.15s;
        }
        .skip-link:hover { color: #94a3b8; }
        .note {
            background: rgba(59,130,246,0.08);
            border: 1px solid rgba(59,130,246,0.2);
            border-radius: 8px;
            padding: 10px 14px;
            color: #93c5fd;
            font-size: 11.5px;
            line-height: 1.5;
            margin-top: 14px;
        }
    </style>
</head>
<body>
<div class="card">
    <div class="brand">
        <div class="brand-icon">🔐</div>
        <div>
            <div class="brand-name">Two-Factor Auth Setup</div>
            <div class="brand-sub">CourierPortal Admin</div>
        </div>
    </div>

    <ul class="step-list">

        <li>
            <div class="step-content">
                <h3>Download Google Authenticator</h3>
                <p>Install the app on your smartphone if you haven't already.</p>
                <div class="store-links">
                    <a href="https://apps.apple.com/app/google-authenticator/id388497605"
                       target="_blank" class="store-link">&#63743; App Store</a>
                    <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2"
                       target="_blank" class="store-link">&#9654; Google Play</a>
                </div>
            </div>
        </li>

        <li>
            <div class="step-content">
                <h3>Scan this QR code with the app</h3>
                <p>Open Google Authenticator, tap the + button, then "Scan a QR code".</p>
                <div class="qr-wrap" style="margin-top:12px;">
                    <div style="background:#ffffff; padding:12px; border-radius:10px; display:inline-block; line-height:0;">
                        {!! $qrCodeSvg !!}
                    </div>
                </div>
            </div>
        </li>

        <li>
            <div class="step-content">
                <h3>Enter the 6-digit code to confirm</h3>
                <p>Type the code shown in Google Authenticator for CourierPortal.</p>
            </div>
        </li>

    </ul>

    <hr class="divider">

    <form method="POST" action="{{ route('admin.2fa.enable') }}">
        @csrf

        @if($errors->any())
            <div class="error-msg">{{ $errors->first() }}</div>
        @endif

        <div class="form-group">
            <label>6-Digit Code</label>
            <input type="text"
                   name="one_time_password"
                   class="otp-input"
                   maxlength="6"
                   pattern="\d{6}"
                   inputmode="numeric"
                   autocomplete="one-time-code"
                   autofocus
                   placeholder="000000"
                   value="{{ old('one_time_password') }}">
        </div>

        <button type="submit" class="btn-enable">Enable Two-Factor Authentication</button>
    </form>

    <a href="{{ route('admin.dashboard') }}" class="skip-link">Skip for now &rarr;</a>

    <div class="note">
        You will need Google Authenticator every time you sign in to the admin panel.
        You can enable or disable 2FA at any time from <strong>Settings</strong>.
    </div>
</div>
</body>
</html>
