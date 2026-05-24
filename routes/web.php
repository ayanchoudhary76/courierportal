<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RateCardController;
use App\Http\Controllers\Admin\TwoFactorController;
use Illuminate\Support\Facades\Route;

// ── Public client routes ─────────────────────────────────────────────
Route::get('/', [App\Http\Controllers\Client\HomeController::class, 'index'])->name('home');
Route::get('/track/{awb?}', [App\Http\Controllers\Client\TrackingController::class, 'index'])->name('tracking.public');

// ── Client auth (guest only) ─────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',  [App\Http\Controllers\Client\AuthController::class, 'showLogin'])->name('client.login');
    Route::post('/login', [App\Http\Controllers\Client\AuthController::class, 'login'])->name('client.login.post')->middleware('throttle:5,1');
    Route::get('/register',  [App\Http\Controllers\Client\AuthController::class, 'showRegister'])->name('client.register');
    Route::post('/register', [App\Http\Controllers\Client\AuthController::class, 'register'])->name('client.register.post')->middleware('throttle:5,1');
    Route::get('/forgot-password',  [App\Http\Controllers\Client\AuthController::class, 'showForgotPassword'])->name('client.password.request');
    Route::post('/forgot-password', [App\Http\Controllers\Client\AuthController::class, 'sendResetLink'])->name('client.password.email');
});
Route::post('/logout', [App\Http\Controllers\Client\AuthController::class, 'logout'])
     ->name('client.logout')->middleware('auth');

// ── Protected client routes ──────────────────────────────────────────
Route::middleware(['auth'])->prefix('client')->name('client.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Client\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/rates',            [App\Http\Controllers\Client\RateController::class, 'index'])->name('rates');
    Route::post('/rates/calculate', [App\Http\Controllers\Client\RateController::class, 'calculate'])->name('rates.calculate');
    Route::get('/book',  [App\Http\Controllers\Client\BookingController::class, 'create'])->name('book');
    Route::post('/book', [App\Http\Controllers\Client\BookingController::class, 'store'])->name('book.store');
    Route::get('/bookings',                       [App\Http\Controllers\Client\BookingController::class, 'index'])->name('bookings');
    Route::get('/bookings/export',                 [App\Http\Controllers\Client\BookingController::class, 'export'])->name('bookings.export');
    Route::get('/bookings/rebook/{awb}',           [App\Http\Controllers\Client\BookingController::class, 'rebook'])->name('book.rebook');
    Route::get('/bookings/{awb}',                  [App\Http\Controllers\Client\BookingController::class, 'show'])->name('bookings.show');
    Route::get('/bookings/{awb}/label',            [App\Http\Controllers\Client\BookingController::class, 'downloadLabel'])->name('bookings.label');
    Route::get('/profile',  [App\Http\Controllers\Client\ProfileController::class, 'edit'])->name('profile');
    Route::put('/profile',  [App\Http\Controllers\Client\ProfileController::class, 'update'])->name('profile.update');

    // Support Tickets
    Route::get('/tickets',                     [App\Http\Controllers\Client\TicketController::class, 'index'])->name('tickets');
    Route::get('/tickets/create',              [App\Http\Controllers\Client\TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets',                    [App\Http\Controllers\Client\TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}',            [App\Http\Controllers\Client\TicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{ticket}/messages',  [App\Http\Controllers\Client\TicketController::class, 'addMessage'])->name('tickets.message');

    // Payment
    Route::post('/payment/initiate', [App\Http\Controllers\Client\PaymentController::class, 'initiate'])->name('payment.initiate');
    Route::post('/payment/verify',   [App\Http\Controllers\Client\PaymentController::class, 'verify'])->name('payment.verify');
    Route::get('/payment/success',   [App\Http\Controllers\Client\PaymentController::class, 'success'])->name('payment.success');
    Route::get('/payment/failed',    [App\Http\Controllers\Client\PaymentController::class, 'failed'])->name('payment.failed');
});

// ── Admin: Public auth ────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login',   [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login',  [AdminAuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    // 2FA: public (no auth needed — user is mid-login)
    Route::get('/2fa/challenge',  [TwoFactorController::class, 'challenge'])->name('2fa.challenge');
    Route::post('/2fa/verify',    [TwoFactorController::class, 'verify'])->name('2fa.verify');
});

// ── Admin: Protected ──────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['admin', 'prevent-client-admin'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Clients
    Route::get('/clients/{client}/bookings', [ClientController::class, 'bookings'])->name('clients.bookings');
    Route::resource('clients', ClientController::class)->names([
        'index' => 'clients.index', 'create' => 'clients.create', 'store' => 'clients.store',
        'show'  => 'clients.show',  'edit'   => 'clients.edit',   'update' => 'clients.update',
        'destroy' => 'clients.destroy',
    ]);

    // Rate Cards
    Route::prefix('rates')->name('rates.')->group(function () {
        Route::get('/', [RateCardController::class, 'index'])->name('index');
        Route::get('/create', [RateCardController::class, 'create'])->name('create');
        Route::post('/', [RateCardController::class, 'store'])->name('store');
        Route::post('/assign-to-client', [RateCardController::class, 'assignToClient'])->name('assign');
        Route::get('/{rateCard}', [RateCardController::class, 'show'])->name('show');
        Route::get('/{rateCard}/edit', [RateCardController::class, 'edit'])->name('edit');
        Route::put('/{rateCard}', [RateCardController::class, 'update'])->name('update');
        Route::delete('/{rateCard}', [RateCardController::class, 'destroy'])->name('destroy');
        Route::post('/{rateCard}/duplicate', [RateCardController::class, 'duplicate'])->name('duplicate');
        Route::post('/{rateCard}/matrix', [RateCardController::class, 'storeMatrix'])->name('matrix.store');
        Route::put('/{rateCard}/matrix/{matrix}', [RateCardController::class, 'updateMatrix'])->name('matrix.update');
        Route::delete('/{rateCard}/matrix/{matrix}', [RateCardController::class, 'destroyMatrix'])->name('matrix.destroy');
        Route::post('/{rateCard}/international', [RateCardController::class, 'storeInternational'])->name('international.store');
        Route::delete('/{rateCard}/international/{rate}', [RateCardController::class, 'destroyInternational'])->name('international.destroy');
    });

    // Bookings
    Route::prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/',                   [App\Http\Controllers\Admin\BookingController::class, 'index'])->name('index');
        Route::get('/export',             [App\Http\Controllers\Admin\BookingController::class, 'export'])->name('export');
        Route::get('/{booking}',          [App\Http\Controllers\Admin\BookingController::class, 'show'])->name('show');
        Route::put('/{booking}/status',   [App\Http\Controllers\Admin\BookingController::class, 'updateStatus'])->name('status');
        Route::post('/{booking}/tracking',[App\Http\Controllers\Admin\BookingController::class, 'addTracking'])->name('tracking');
        Route::get('/{booking}/label',    [App\Http\Controllers\Admin\BookingController::class, 'printLabel'])->name('label');
    });

    // Support Tickets
    Route::prefix('tickets')->name('tickets.')->group(function () {
        Route::get('/',                          [App\Http\Controllers\Admin\TicketController::class, 'index'])->name('index');
        Route::get('/{ticket}',                  [App\Http\Controllers\Admin\TicketController::class, 'show'])->name('show');
        Route::post('/{ticket}/messages',        [App\Http\Controllers\Admin\TicketController::class, 'addMessage'])->name('message');
        Route::put('/{ticket}/status',           [App\Http\Controllers\Admin\TicketController::class, 'updateStatus'])->name('status');
        Route::put('/{ticket}/assign',           [App\Http\Controllers\Admin\TicketController::class, 'assign'])->name('assign');
    });

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/',          [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('index');
        Route::get('/bookings',  [App\Http\Controllers\Admin\ReportController::class, 'bookings'])->name('bookings');
        Route::get('/revenue',   [App\Http\Controllers\Admin\ReportController::class, 'revenue'])->name('revenue');
        Route::get('/clients',   [App\Http\Controllers\Admin\ReportController::class, 'clients'])->name('clients');
    });

    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/',           [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('index');
        Route::put('/profile',    [App\Http\Controllers\Admin\SettingsController::class, 'updateProfile'])->name('profile');
        Route::put('/password',   [App\Http\Controllers\Admin\SettingsController::class, 'updatePassword'])->name('password');
    });

    // 2FA: protected (user must be authenticated to setup/disable)
    Route::prefix('2fa')->name('2fa.')->group(function () {
        Route::get('/setup',    [TwoFactorController::class, 'setup'])->name('setup');
        Route::post('/enable',  [TwoFactorController::class, 'enable'])->name('enable');
        Route::post('/disable', [TwoFactorController::class, 'disable'])->name('disable');
    });
});
