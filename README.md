# 🚚 CourierPortal — Courier Booking Web Portal

A full-featured, production-grade courier booking and logistics management platform built with **Laravel 11** and **MySQL 8.0**. Inspired by DTDC, Shiprocket, FedEx, and DHL — with per-client dynamic rate engines, live shipment tracking, support ticket system, and GST-compliant invoice generation.

---

## 📋 Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Project Structure](#project-structure)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Database Setup](#database-setup)
- [Running the Application](#running-the-application)
- [Test Credentials](#test-credentials)
- [Module Overview](#module-overview)
- [Route Summary](#route-summary)
- [Key Design Decisions](#key-design-decisions)
- [Production Deployment](#production-deployment)
- [Remaining Pre-Launch Tasks](#remaining-pre-launch-tasks)
- [Contributing](#contributing)
- [License](#license)

---

## ✨ Features

### Admin Side
- 🔐 Secure admin login with Two-Factor Authentication (Google Authenticator)
- 📊 Real-time operations dashboard (bookings, revenue, tickets, delivery stats)
- 👥 Full client management (CRUD, suspend, assign rate cards)
- 💰 Per-client dynamic rate card engine with zone matrix, surcharges, and versioning
- 📦 Booking management — status updates, tracking event injection, bulk AWB label print
- 🎫 Support ticket management with internal notes, SLA alerts, team assignment
- 📈 Reports — revenue, bookings, client activity, outstanding bills
- 📤 Excel/CSV export for all major data sets
- 🔍 Full audit log — every admin action recorded with IP and timestamp

### Client Side
- 🏠 Trust-building homepage — stats, testimonials, FAQ, Google Maps, WhatsApp CTA
- 🧮 Rate Calculator — domestic (pincode-to-pincode, zone detection) and international
- 📝 6-step booking wizard — sender, receiver, parcel, pickup, review, confirm
- 📡 Live shipment tracking — visual timeline with event history (no login required)
- 📄 AWB label PDF download (100mm × 150mm, barcode + QR)
- 💳 Payment — Razorpay (UPI, Cards, NetBanking) or Bill-to-Account (credit clients)
- 🎫 Support ticket system — raise, track, reply to queries
- 📁 Booking history with filters, re-book, and Excel export
- 👤 Profile management

---

## 🛠 Tech Stack

| Layer | Technology |
|---|---|
| Backend Framework | Laravel 11 (PHP 8.3) |
| Frontend | Blade + Alpine.js + Tailwind CSS (via Breeze) |
| Database | MySQL 8.0 |
| Cache / Sessions | Redis (file/database fallback for local dev) |
| PDF Generation | barryvdh/laravel-dompdf |
| Excel Export | maatwebsite/laravel-excel |
| Barcode / QR | milon/barcode |
| Payment Gateway | Razorpay PHP SDK (stub — ready to activate) |
| 2FA | pragmarx/google2fa-laravel |
| Auth | Laravel Breeze (Blade) + Sanctum |
| Queue | Laravel Queue (database driver locally, Redis in production) |
| Asset Bundling | Vite |

---

## 📁 Project Structure

```
courier-portal/
├── app/
│   ├── Exports/                    # Excel export classes
│   │   ├── AdminBookingsExport.php
│   │   └── ClientBookingsExport.php
│   ├── Helpers/
│   │   └── AuditHelper.php         # Static audit log writer
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/              # Admin-side controllers
│   │   │   │   ├── AuthController.php
│   │   │   │   ├── BookingController.php
│   │   │   │   ├── ClientController.php
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── RateCardController.php
│   │   │   │   ├── ReportController.php
│   │   │   │   └── TicketController.php
│   │   │   └── Client/             # Client-side controllers
│   │   │       ├── AuthController.php
│   │   │       ├── BookingController.php
│   │   │       ├── DashboardController.php
│   │   │       ├── HomeController.php
│   │   │       ├── PaymentController.php
│   │   │       ├── ProfileController.php
│   │   │       ├── RateController.php
│   │   │       ├── TicketController.php
│   │   │       └── TrackingController.php
│   │   └── Middleware/
│   │       ├── AdminMiddleware.php
│   │       ├── PreventClientAccessToAdmin.php
│   │       └── SecurityHeaders.php
│   ├── Mail/
│   │   ├── BookingStatusMail.php
│   │   └── TicketReplyMail.php
│   ├── Models/
│   │   ├── AuditLog.php
│   │   ├── Booking.php
│   │   ├── Client.php
│   │   ├── InternationalRate.php
│   │   ├── RateCard.php
│   │   ├── RateMatrix.php
│   │   ├── SupportTicket.php
│   │   ├── TicketMessage.php
│   │   ├── TrackingEvent.php
│   │   └── User.php
│   ├── Services/
│   │   ├── AwbService.php          # Collision-free AWB generator (CP{YY}{8-digit})
│   │   └── TicketService.php       # Ticket number generator (TKT-YYYYMM-XXXX)
│   └── View/Components/
│       └── StatusBadge.php         # Reusable status badge component
├── database/
│   ├── migrations/                 # 15 migrations (12 core + 3 supplemental)
│   └── seeders/
│       ├── AdminSeeder.php         # Production-safe admin user
│       └── TestDataSeeder.php      # 3 clients, 151 rate rows, 5 bookings, 2 tickets
├── resources/views/
│   ├── admin/                      # Admin Blade views
│   │   ├── layouts/app.blade.php   # Dark sidebar layout
│   │   ├── auth/login.blade.php
│   │   ├── bookings/
│   │   ├── clients/
│   │   ├── dashboard.blade.php
│   │   ├── rates/
│   │   ├── reports/
│   │   └── tickets/
│   ├── client/                     # Client Blade views
│   │   ├── layouts/app.blade.php   # Sticky navbar + footer layout
│   │   ├── auth/
│   │   ├── bookings.blade.php
│   │   ├── booking-show.blade.php
│   │   ├── book.blade.php          # 6-step Alpine.js wizard
│   │   ├── dashboard.blade.php
│   │   ├── home.blade.php          # 9-section landing page
│   │   ├── label-pdf.blade.php     # DomPDF AWB label
│   │   ├── profile.blade.php
│   │   ├── rates.blade.php         # Live rate calculator
│   │   ├── tickets/
│   │   └── tracking.blade.php
│   ├── components/
│   │   └── status-badge.blade.php  # x-status-badge component
│   └── emails/
│       ├── booking-status.blade.php
│       └── ticket-reply.blade.php
├── routes/
│   └── web.php                     # 76 routes total
├── PRODUCTION_CHECKLIST.md
└── README.md
```

---

## ✅ Requirements

- PHP 8.3+ with extensions: `gd`, `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`
- MySQL 8.0+
- Composer 2.x
- Node.js 18+ and npm
- Redis (optional locally, required in production)
- XAMPP / Laragon / Herd (local dev)

---

## 🚀 Installation

```bash
# 1. Clone the repository
git clone https://github.com/your-username/courier-portal.git
cd courier-portal

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies and build assets
npm install && npm run build

# 4. Copy environment file
cp .env.example .env

# 5. Generate application key
php artisan key:generate
```

---

## ⚙️ Configuration

Open `.env` and update the following:

```env
APP_NAME=CourierPortal
APP_URL=http://127.0.0.1:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=courier_portal
DB_USERNAME=root
DB_PASSWORD=

# For local dev (no Redis installed)
CACHE_STORE=file
SESSION_DRIVER=database
QUEUE_CONNECTION=database

# For production (Redis required)
# CACHE_STORE=redis
# SESSION_DRIVER=redis
# QUEUE_CONNECTION=redis

# Mail (required for ticket reply + booking status emails)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=your_mailgun_username
MAIL_PASSWORD=your_mailgun_password
MAIL_FROM_ADDRESS=noreply@yourcourier.com
MAIL_FROM_NAME="CourierPortal"

# Razorpay (activate when ready)
RAZORPAY_KEY=your_razorpay_key_here
RAZORPAY_SECRET=your_razorpay_secret_here
```

> **XAMPP users:** Make sure `extension=gd` is uncommented in your `php.ini`. Required by `maatwebsite/excel` and `milon/barcode`.

---

## 🗄 Database Setup

```bash
# 1. Create the database in MySQL
# (run in phpMyAdmin or MySQL CLI)
CREATE DATABASE courier_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# 2. Run all migrations (15 tables)
php artisan migrate

# 3a. Seed admin user only (for production)
php artisan db:seed --class=AdminSeeder

# 3b. Seed full test data (for local development)
php artisan db:seed --class=TestDataSeeder

# 4. Create sessions table (required for database session driver)
php artisan session:table
php artisan migrate

# 5. Link storage (for ticket attachments)
php artisan storage:link
```

### Database Schema (12 Core Tables)

| Table | Purpose |
|---|---|
| `users` | Auth for both admins and clients |
| `clients` | Client company profile, rate card assignment |
| `rate_cards` | Named rate presets (Standard, Premium, etc.) |
| `rate_matrix` | Domestic rate rows (service × zone × weight slab) |
| `international_rates` | International rate rows (country group × weight) |
| `bookings` | All shipment bookings with AWB numbers |
| `tracking_events` | Timeline events per booking |
| `invoices` | GST-compliant invoice records |
| `payments` | Razorpay + Bill-to-Account payment records |
| `support_tickets` | Client query tickets |
| `ticket_messages` | Thread messages per ticket |
| `audit_log` | Admin action log with old/new values |

---

## ▶️ Running the Application

```bash
# Start the development server
php artisan serve

# In a separate terminal, watch assets (if editing CSS/JS)
npm run dev

# Run queue worker (needed for email jobs)
php artisan queue:work
```

Open [http://127.0.0.1:8000](http://127.0.0.1:8000) in your browser.

---

## 🔑 Test Credentials

> These are for local development only. **Do not seed TestDataSeeder in production.**

| Role | Email | Password |
|---|---|---|
| Admin | admin@courierportal.com | Admin@123456 |
| Client — StyleCart Delhi (prepaid, 5 bookings) | stylecart@test.com | Client@123456 |
| Client — TechSupplies Mumbai (credit ₹50,000 limit) | techsupplies@test.com | Client@123456 |
| Client — Handcraft Jaipur (prepaid) | handcraft@test.com | Client@123456 |

---

## 📦 Module Overview

### Admin Modules

#### Dashboard `/admin/dashboard`
Real-time stats: today's bookings, pending pickups, in-transit count, delivered today, open tickets, revenue, new clients, failed deliveries, outstanding bills. Recent bookings table with clickable AWB links.

#### Client Management `/admin/clients`
Full CRUD with DB transactions. Search by name/email/city/phone. Alpine.js credit limit toggle. Suspend (soft-disable) instead of delete. Per-client rate card assignment.

#### Rate Card Management `/admin/rates`
Most technically complex module. Features:
- Create named rate templates (Standard, Premium, E-commerce)
- Domestic matrix: weight slabs × zones (A–E) × service types
- International rates: country groups × weight slabs
- Duplicate a rate card with one click
- Assign to individual clients; override at booking level
- 3-tab Alpine.js interface (Domestic Matrix / International / Assigned Clients)

#### Booking Management `/admin/bookings`
View, filter, and update all bookings. Inject manual tracking events. Print AWB label PDF. Status changes auto-create tracking timeline entries and send email notifications.

#### Support Tickets `/admin/tickets`
Filter by status/category. SLA highlighting (red row if open > 48 hours). Assign to team member. Internal notes (not visible to client). Reply triggers queued email to client.

#### Reports `/admin/reports`
- Booking report with date range, status, and service filters
- Revenue report with daily breakdown and top-10 clients
- Client activity report with booking counts and revenue totals

### Client Modules

#### Homepage `/`
9 sections: Hero banner, animated stats strip, trust pillars, 3-step how-it-works, quick track widget, testimonials, contact + Google Maps placeholder, FAQ accordion (Alpine.js), CTA banner.

#### Rate Calculator `/client/rates`
Live rates from the client's assigned rate card. Domestic tab: pincode-based zone detection + dimensional weight. International tab: country group pricing. Full charge breakdown: base freight + fuel surcharge + ODA + GST.

#### Booking Wizard `/client/book`
6-step Alpine.js wizard: Sender → Receiver → Parcel → Pickup → Review → Confirm. Server-side rate re-validation on submit. AWB auto-generated (CP + year + 8 digits, collision-safe). First tracking event auto-created.

#### Live Tracking `/track/{awb}`
No login required. Vertical timeline with completed (filled blue), current (pulsing), and future (gray) steps. Shows event location and timestamp for each completed step.

#### Support Tickets `/client/tickets`
Raise queries with category, AWB reference, and file attachment. Chat-style thread view. Status indicator per ticket.

---

## 🗺 Route Summary

```
Total: 76 routes

Public
  GET  /                          Homepage
  GET  /track/{awb?}              Public shipment tracking

Client Auth (guest only, throttled)
  GET  /login                     Login page
  POST /login                     Authenticate (throttle: 5/min)
  GET  /register                  Register page
  POST /register                  Create account (throttle: 5/min)
  GET  /forgot-password           Forgot password form
  POST /forgot-password           Send reset link

Protected Client Routes (/client/*)
  GET  /client/dashboard          Client dashboard
  GET  /client/rates              Rate calculator
  POST /client/rates/calculate    AJAX rate calculation
  GET  /client/book               Booking wizard
  POST /client/book               Submit booking
  GET  /client/book/rebook/{awb}  Re-book from history
  GET  /client/bookings           Booking history
  GET  /client/bookings/export    Export to Excel
  GET  /client/bookings/{awb}     Booking detail
  GET  /client/bookings/{awb}/label  Download AWB label PDF
  GET  /client/tickets            Ticket list
  GET  /client/tickets/create     Raise ticket form
  POST /client/tickets            Submit ticket
  GET  /client/tickets/{ticket}   Ticket thread
  POST /client/tickets/{ticket}/messages  Add reply
  GET  /client/profile            Edit profile
  PUT  /client/profile            Save profile
  POST /client/payment/*          Payment routes (Razorpay stub)

Admin Routes (/admin/*)
  GET  /admin/login               Admin login
  POST /admin/login               Authenticate
  POST /admin/logout              Logout
  GET  /admin/dashboard           Dashboard
  GET|POST|PUT|DELETE /admin/clients/*     Client CRUD
  GET|POST|PUT|DELETE /admin/rates/*       Rate card CRUD + matrix
  GET|PUT /admin/bookings/*                Booking management
  GET|PUT|POST /admin/tickets/*            Ticket management
  GET  /admin/reports/*           Reports (4 sub-pages)
```

---

## 🧠 Key Design Decisions

**Zone Detection** — Domestic shipping zones (A–E) are derived from origin and destination pincode prefixes. This is a simplified approximation; replace with a full pincode-to-zone lookup table for production accuracy.

**Rate Hierarchy** — Global default → Rate template → Client override → Single booking override. The client's `rate_card_id` is resolved at login; rate changes by admin take effect on the next page load.

**AWB Generation** — Format: `CP{YY}{8-digit-random}` (e.g. `CP2505001234`). A do-while loop checks for uniqueness before inserting. Collision probability is negligible at any realistic booking volume.

**Soft Delete for Clients** — Clients are never hard-deleted. Suspending sets `is_active = false` on both `users` and `clients` tables. This preserves all booking and payment history.

**DomPDF Label** — AWB labels use inline CSS only (no Tailwind) because DomPDF does not load external stylesheets. Label size is 100mm × 150mm.

**Security** — CSRF protection (Laravel built-in), parameterized queries (Eloquent), bcrypt password hashing, throttled auth routes, `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy` headers on all responses, role-based middleware on every route group.

---

## 🚢 Production Deployment

See **`PRODUCTION_CHECKLIST.md`** in the project root for the complete checklist. Summary:

```bash
# 1. Set production environment
APP_ENV=production
APP_DEBUG=false
SESSION_SECURE_COOKIE=true
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# 2. Cache everything
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 3. Run migrations (production DB)
php artisan migrate --force

# 4. Seed admin only (NOT TestDataSeeder)
php artisan db:seed --class=AdminSeeder

# 5. Storage link
php artisan storage:link

# 6. Start queue worker (via Supervisor)
php artisan queue:work --daemon
```

**Server requirements:** PHP 8.3, MySQL 8.0, Redis, Nginx/Apache with `try_files` config, SSL certificate, writable `storage/` and `bootstrap/cache/` directories.

---

## 🔧 Remaining Pre-Launch Tasks

| Task | Effort | Notes |
|---|---|---|
| Razorpay live integration | 1–2 days | `composer require razorpay/razorpay`, wire `PaymentController`, test sandbox first |
| SMTP / Mailgun setup | 2–3 hours | Add `MAIL_*` env vars; queued mail jobs already exist |
| Queue worker (Supervisor) | 1 hour | Required for email delivery in production |
| Admin 2FA activation | 30 mins | Package already installed (`pragmarx/google2fa-laravel`) |
| Google Maps embed | 30 mins | Replace placeholder in `home.blade.php` with real embed URL |
| SMS notifications (MSG91) | 1 day | Optional for launch; add to `BookingController` and `Admin\BookingController` |
| Pincode-to-zone lookup table | 1–2 days | Replace approximate zone logic with full India pincode database |
| S3 file storage | 2–3 hours | For ticket attachments in production; configure `AWS_*` env vars |

---

## 📄 License

This project is proprietary software. All rights reserved.  
Built for internal logistics business use. Not licensed for redistribution.

---

## 👤 Author

Built with Laravel 11 + MySQL 8.0  
Developed using AI-assisted development workflow (Anthropic Claude + Antigravity)  
Version: 1.0 | Stack: Laravel 11 · MySQL 8.0 · Alpine.js · Tailwind CSS