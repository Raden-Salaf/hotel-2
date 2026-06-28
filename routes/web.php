<?php

use Illuminate\Support\Facades\Route;

// ============================================
// IMPORT SEMUA CONTROLLER
// ============================================

// Controller untuk dashboard (semua role)
use App\Http\Controllers\DashboardController;

// Controller Super Admin
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\RoomCategoryController;
use App\Http\Controllers\Admin\FnbCategoryController;
use App\Http\Controllers\Admin\FnbItemController;
use App\Http\Controllers\Admin\UserController;

// Controller Resepsionis
use App\Http\Controllers\Resepsionis\BookingController;

// Controller FnB
use App\Http\Controllers\FnB\FnbOrderController;

// Controller Halaman Publik (tamu booking online)
use App\Http\Controllers\Public\PublicBookingController;

// Controller untuk loundry
// Tambahkan import di atas
use App\Http\Controllers\Admin\LaundryItemController;
use App\Http\Controllers\Laundry\LaundryOrderController;

// ============================================
// HALAMAN PUBLIK — tidak butuh login
// Tamu bisa akses untuk booking kamar secara online
// ============================================
Route::prefix('booking')->name('public.')->group(function () {

     // Halaman utama — tampilkan semua kamar tersedia
     // URL: GET /booking
     Route::get('/', [PublicBookingController::class, 'index'])
          ->name('booking.index');

     // Halaman detail kamar + form isi data booking
     // URL: GET /booking/rooms/{room}
     Route::get('/rooms/{room}', [PublicBookingController::class, 'show'])
          ->name('booking.show');

     // Proses submit form booking dari tamu
     // URL: POST /booking/rooms/{room}
     Route::post('/rooms/{room}', [PublicBookingController::class, 'store'])
          ->name('booking.store');

     // Halaman konfirmasi setelah booking berhasil dibuat
     // URL: GET /booking/confirmation/{booking}
     Route::get('/confirmation/{booking}', [PublicBookingController::class, 'confirmation'])
          ->name('booking.confirmation');

     // ============================================
     // MIDTRANS WEBHOOK — dipanggil otomatis oleh Midtrans
     // setelah tamu selesai bayar di halaman Midtrans
     // URL: POST /booking/payment/callback
     // Tidak pakai CSRF karena request dari server Midtrans
     // ============================================
     Route::post('/payment/callback', [PublicBookingController::class, 'paymentCallback'])
          ->name('payment.callback')
          ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

     // Halaman setelah tamu selesai bayar (redirect dari Midtrans)
     // URL: GET /booking/payment/finish/{booking}
     // Route::get('/payment/finish/{booking}', [PublicBookingController::class, 'paymentFinish'])
     //      ->name('payment.finish');

     Route::get('/payment/finish/{bookingId}', [PublicBookingController::class, 'paymentFinish'])
          ->name('payment.finish');

     // ← TAMBAHKAN INI — cek ulang status pembayaran untuk VA
     // Route::get('/payment/check/{booking}', [PublicBookingController::class, 'checkPayment'])
     //      ->name('payment.check');
     Route::get('/payment/check/{bookingId}', [PublicBookingController::class, 'checkPayment'])
          ->name('payment.check');
});

// ============================================
// HALAMAN UTAMA
// Redirect ke halaman booking publik
// ============================================
Route::get('/', function () {
     return redirect()->route('public.booking.index');
});

// ============================================
// ROUTES YANG BUTUH LOGIN
// Semua route di dalam group ini harus login dulu
// ============================================
Route::middleware(['auth'])->group(function () {

     // Dashboard — semua role bisa akses
     // Konten dashboard berbeda tergantung role yang login
     // URL: GET /dashboard
     Route::get('/dashboard', [DashboardController::class, 'index'])
          ->name('dashboard');

     // ==========================================
     // SUPER ADMIN ROUTES
     // Hanya super_admin yang bisa akses
     // Prefix: /admin/...
     // Name prefix: admin....
     // ==========================================
     Route::middleware(['role:super_admin'])
          ->prefix('admin')
          ->name('admin.')
          ->group(function () {

               // Manajemen User (CRUD)
               // URL: /admin/users
               Route::resource('users', UserController::class);

               // Manajemen Kategori Kamar (CRUD)
               // URL: /admin/room-categories
               Route::resource('room-categories', RoomCategoryController::class);

               // Manajemen Kamar (CRUD)
               // URL: /admin/rooms
               Route::resource('rooms', RoomController::class);

               // Manajemen Kategori F&B (CRUD)
               // URL: /admin/fnb-categories
               Route::resource('fnb-categories', FnbCategoryController::class);

               // Manajemen Menu F&B (CRUD)
               // URL: /admin/fnb-items
               Route::resource('fnb-items', FnbItemController::class);

               // Manajemen Laundry(CRUD)
               Route::resource('laundry', LaundryItemController::class);
          });

     // ==========================================
     // RESEPSIONIS ROUTES
     // Bisa diakses oleh resepsionis DAN super_admin
     // Prefix: /resepsionis/...
     // Name prefix: resepsionis....
     // ==========================================
     Route::middleware(['role:resepsionis,super_admin'])
          ->prefix('resepsionis')
          ->name('resepsionis.')
          ->group(function () {

               // CRUD Booking (list, create, show, edit, update, delete)
               // URL: /resepsionis/bookings
               Route::resource('bookings', BookingController::class);

               // Konfirmasi booking online (pending → confirmed)
               // URL: PATCH /resepsionis/bookings/{booking}/confirm
               Route::patch('bookings/{booking}/confirm', [BookingController::class, 'confirm'])
                    ->name('bookings.confirm');

               // Check-in tamu (confirmed → checked_in)
               // URL: PATCH /resepsionis/bookings/{booking}/check-in
               Route::patch('bookings/{booking}/check-in', [BookingController::class, 'checkIn'])
                    ->name('bookings.check-in');

               // Check-out tamu (checked_in → checked_out)
               // URL: PATCH /resepsionis/bookings/{booking}/check-out
               Route::patch('bookings/{booking}/check-out', [BookingController::class, 'checkOut'])
                    ->name('bookings.check-out');

               // Tampilkan halaman invoice booking
               // URL: GET /resepsionis/bookings/{booking}/invoice
               Route::get('bookings/{booking}/invoice', [BookingController::class, 'invoice'])
                    ->name('bookings.invoice');
          });

     // ==========================================
     // FNB ADMIN ROUTES
     // Bisa diakses oleh admin_fnb DAN super_admin
     // Prefix: /fnb/...
     // Name prefix: fnb....
     // ==========================================
     Route::middleware(['role:admin_fnb,super_admin'])
          ->prefix('fnb')
          ->name('fnb.')
          ->group(function () {

               // Manajemen Menu F&B — admin FnB bisa edit menu juga
               // URL: /fnb/items
               Route::resource('items', FnbItemController::class);

               Route::get('orders/create', [FnbOrderController::class, 'create'])
                    ->name('orders.create');

               Route::post('orders/create', [FnbOrderController::class, 'storeOrder'])
                    ->name('orders.store-order');

               // Kelola pesanan FnB yang masuk dari booking
               // Hanya index (list), show (detail), update (ubah status)
               // URL: /fnb/orders
               Route::resource('orders', FnbOrderController::class)
                    ->only(['index', 'show', 'update']);

               // Update status pesanan: pending → preparing → delivered
               // URL: PATCH /fnb/orders/{bookingItem}/status
               Route::patch('orders/{bookingItem}/status', [FnbOrderController::class, 'updateStatus'])
                    ->name('orders.update-status');
          });

     // ==========================================
     // LAUNDRY ROUTES
     // Bisa diakses oleh resepsionis DAN super_admin
     // ==========================================
     Route::middleware(['role:resepsionis,super_admin'])
          ->prefix('laundry')
          ->name('laundry.')
          ->group(function () {

               // Buat pesanan laundry — harus sebelum resource
               Route::get('orders/create', [LaundryOrderController::class, 'create'])
                    ->name('orders.create');

               Route::post('orders/store', [LaundryOrderController::class, 'store'])
                    ->name('orders.store');

               // List & detail pesanan
               Route::resource('orders', LaundryOrderController::class)
                    ->only(['index', 'show']);

               // Update status: pending → processing → done
               Route::patch('orders/{order}/status', [LaundryOrderController::class, 'updateStatus'])
                    ->name('orders.update-status');
          });
});

// ============================================
// AUTH ROUTES — dari Laravel Breeze
// login, logout, register, forgot password, dll
// File: routes/auth.php
// ============================================
require __DIR__ . '/auth.php';
