# CourierPortal — Production Deployment Checklist

## Before Going Live

### Environment
- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Set `APP_URL=https://yourdomain.com` in `.env`
- [ ] Set `SESSION_SECURE_COOKIE=true` (requires HTTPS)
- [ ] Set `SESSION_SAME_SITE=lax`
- [ ] Fill `RAZORPAY_KEY` and `RAZORPAY_SECRET` (get from [razorpay.com](https://razorpay.com))
- [ ] Configure mail driver: `MAIL_MAILER=smtp`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD` (Mailgun / SendGrid recommended)
- [ ] Set `MAIL_FROM_ADDRESS` and `MAIL_FROM_NAME`
- [ ] Set `CACHE_STORE=redis` and `SESSION_DRIVER=redis` (install phpredis extension)
- [ ] Set `QUEUE_CONNECTION=redis` and run: `php artisan queue:work --daemon` (or use Supervisor)

### Database
- [ ] Run `php artisan migrate --force` on production DB
- [ ] Run `php artisan db:seed --class=AdminSeeder` (production admin only — do NOT run TestDataSeeder)
- [ ] Set up daily MySQL dump cron to S3 or backup storage
- [ ] Enable MySQL slow query log
- [ ] Ensure `ticket_messages.is_internal` column exists (`php artisan migrate:status`)

### Storage & Assets
- [ ] Run `php artisan storage:link` (enables ticket attachment downloads)
- [ ] Configure S3 or cloud storage for uploaded ticket attachments: `FILESYSTEM_DISK=s3`
- [ ] Set max upload size in `php.ini`: `upload_max_filesize=10M`, `post_max_size=12M`

### Server Configuration
- [ ] PHP 8.2+ with extensions: `gd`, `redis`, `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `zip` (for Excel exports)
- [ ] Set document root to `/public`
- [ ] Nginx config: `try_files $uri $uri/ /index.php?$query_string`
- [ ] SSL certificate (Let's Encrypt or commercial)
- [ ] Run: `php artisan config:cache && php artisan route:cache && php artisan view:cache`
- [ ] Set folder permissions: `storage/` and `bootstrap/cache/` must be writable (`755` or `775`)

### Queue Worker (Supervisor example)
```ini
[program:courier-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopaseconds=10
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/html/storage/logs/worker.log
```

### Third-party Services
- [ ] **Razorpay**: Test with `test_` keys → switch to live keys after verification
- [ ] **SMS (optional)**: MSG91 or Twilio for shipment alerts — add `SMS_PROVIDER` to `.env`
- [ ] **Sentry**: Add `SENTRY_LARAVEL_DSN` in `.env` for error tracking
- [ ] **Google2FA**: Test admin 2FA setup if `pragmarx/google2fa-laravel` is activated

### Final Verification Before Launch
- [ ] Create one real client account end-to-end
- [ ] Complete full booking flow: Calculator → Book → Track → Label Print
- [ ] Test Razorpay payment in **test mode** before switching to live keys
- [ ] Test AWB label PDF print at 100mm × 150mm
- [ ] Test support ticket create/reply/email notification flow
- [ ] Test admin status update email notification
- [ ] Test bookings export (`.xlsx` download) from admin and client sides
- [ ] Test rebook functionality (pre-filled booking wizard)
- [ ] Test on mobile: Android Chrome + iOS Safari (responsive layouts)
- [ ] Run `php artisan route:list | grep -c GET` — confirm route count ≥ 78
- [ ] Check all admin routes require `admin` role (test with client cookie)
- [ ] Verify `X-Frame-Options: SAMEORIGIN` header on responses

---

## Route Summary (77 routes total)

| Group          | Routes | Key Endpoints |
|---|---|---|
| Public         | 2      | `/`, `/track/{awb?}` |
| Client Auth    | 6      | `/login`, `/register`, `/logout` |
| Client Portal  | 20+    | `/client/dashboard`, `/client/book`, `/client/bookings`, `/client/rates`, `/client/tickets` |
| Admin Auth     | 3      | `/admin/login`, `/admin/logout` |
| Admin Portal   | 40+    | `/admin/dashboard`, `/admin/bookings`, `/admin/clients`, `/admin/rates`, `/admin/tickets`, `/admin/reports` |

---

## Security Hardening Summary

| Feature | Status | Detail |
|---|---|---|
| Admin role check | ✅ | `AdminMiddleware` + `PreventClientAccessToAdmin` |
| Client booking ownership | ✅ | `where('client_id', $client->id)` in show/label/rebook |
| Client ticket ownership | ✅ | `where('client_id', $client->id)` in show/addMessage |
| Auth rate limiting | ✅ | `throttle:5,1` on POST /login and /register |
| Security headers | ✅ | `SecurityHeaders` global middleware (X-Frame-Options, X-Content-Type-Options, Referrer-Policy, XSS-Protection) |
| Session security | ✅ | `SESSION_SECURE_COOKIE`, `SESSION_SAME_SITE=lax` |
| CSRF protection | ✅ | Laravel default `@csrf` on all forms |
| Mass assignment | ✅ | `$fillable` on all models |

---

## What Still Needs to Be Done Before Launch

1. **Razorpay SDK**: Replace stub with `razorpay/razorpay` composer package and implement order creation + webhook verification
2. **Email configuration**: Set up SMTP/Mailgun credentials — currently mails are queued but won't send without a driver
3. **Queue worker**: Deploy Supervisor to run `php artisan queue:work` — emails and export jobs queue but need a worker
4. **SMS notifications**: Optional but recommended for delivery alerts
5. **Admin 2FA**: Activate `pragmarx/google2fa-laravel` for admin login
6. **File upload to S3**: Currently uploads go to local `storage/app/public` — configure S3 for production
7. **Automated rate zone mapping**: Currently uses pincode-prefix heuristics — integrate a real zone API for accuracy
8. **Cron schedule**: `php artisan schedule:run` every minute for maintenance tasks
