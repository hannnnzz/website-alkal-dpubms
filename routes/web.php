<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\AdminDashboardController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\User\UserDashboardController;
use App\Http\Controllers\Admin\AdminAlatSewaTypeController;
use App\Http\Controllers\Admin\AdminUjiTypeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// LANDING
Route::get('/', function () {
    return view('welcome');
});

// REDIRECT DASHBOARD UTAMA
Route::middleware(['auth', 'verified'])->get('/dashboard', function () {
    return auth()->user()->role === 'admin'
        ? redirect()->route('admin.dashboard')
        : redirect()->route('user.dashboard');
})->name('dashboard');

// ADMIN ROUTES
Route::middleware(['auth','admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard admin
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Resource (middleware sudah ada di group)
    Route::resource('alat-sewa-types', AdminAlatSewaTypeController::class);
    Route::resource('uji-types', AdminUjiTypeController::class);

    // Orders admin
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/export', [AdminOrderController::class, 'export'])->name('orders.export');
    Route::put('/orders/{id}', [AdminOrderController::class, 'update'])->name('orders.update');
    Route::get('/orders/{id}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{id}/download-file', [AdminOrderController::class, 'downloadFile'])->name('orders.downloadFile');

    // Invoice order
    Route::get('/orders/{id}/invoice', [AdminOrderController::class, 'invoice'])->name('orders.invoice');
    Route::get('/orders/{id}/invoice/print', [AdminOrderController::class, 'invoicePrint'])->name('orders.invoice.print');
    Route::get('/orders/{id}/invoice/pdf', [AdminOrderController::class, 'invoicePdf'])->name('orders.invoice.pdf');

    // Invoice Edit + Update
    Route::get('/orders/{id}/invoice/edit', [AdminOrderController::class, 'editInvoice'])->name('orders.invoice.edit');
    Route::post('/orders/{id}/invoice', [AdminOrderController::class, 'updateInvoice'])->name('orders.invoice.update');

    // Lock alat sewa
    Route::post('/alat-sewa-types/{alat_sewa_type}/toggle-lock', [AdminAlatSewaTypeController::class, 'toggleLock'])->name('alat-sewa-types.toggleLock');

    // Admin cancel order
    Route::post('/orders/{id}/cancel', [AdminOrderController::class, 'cancel'])->name('orders.cancel');

    // Admin Edit Tanggal
    Route::patch('/orders/{id}/item/{item}', [AdminOrderController::class, 'updateItem'])->name('orders.item.update');

});

// USER ROUTES (gabungkan semua user routes di sini)
Route::middleware(['auth'])->prefix('user')->name('user.')->group(function () {
    // Dashboard user
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');

    // Alat Sewa
    Route::post('/alats/check-availability', [OrderController::class, 'checkAvailability'])->name('alats.checkAvailability');
    Route::get('/alats/booked-dates/{alat}', [OrderController::class, 'getBookedDates'])->name('alats.bookedDates');

    // Create forms
    Route::get('/order/uji', [OrderController::class, 'createuji'])->name('order.createuji');
    Route::get('/order/sewa', [OrderController::class, 'createsewa'])->name('order.createsewa');

    // Store
    Route::post('/order/uji', [OrderController::class, 'storeuji'])->name('order.storeuji');
    Route::post('/order/sewa', [OrderController::class, 'storesewa'])->name('order.storesewa');

    // History & detail
    Route::get('/order', [OrderController::class, 'index'])->name('order.index');
    Route::get('/order/{id}', [OrderController::class, 'show'])->name('order.show');
    Route::get('/order/{id}/invoice/pdf', [OrderController::class, 'invoicePdf'])->name('order.invoice.pdf');

    // UPDATE lokasi per item (AJAX / fallback form)
    Route::patch('/order/{id}/item/{item}', [OrderController::class, 'updateItem'])->name('order.item.update');

    // Cancel order (user)
    Route::post('/order/{id}/cancel', [OrderController::class, 'cancel'])->name('order.cancel');
});

// PROFILE
Route::middleware('auth')->group(function () {
    Route::get('/profile',   [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile',[ProfileController::class, 'destroy'])->name('profile.destroy');
});

//GET STORAGE
Route::get('/storage/{path}', function ($path) {
    $path = str_replace(['..', '\\'], '', $path);

    $publicFull = storage_path('app/public/' . $path);
    if (file_exists($publicFull)) {
        return response()->file($publicFull);
    }

    $appUploadsFull = storage_path('app/uploads/' . $path);
    if (file_exists($appUploadsFull)) {
        return response()->file($appUploadsFull);
    }

    $uploadsFull = storage_path('uploads/' . $path);
    if (file_exists($uploadsFull)) {
        return response()->file($uploadsFull);
    }

    abort(404);
})->where('path', '.*');


// PAYMENT (QRIS) - auth required
Route::middleware('auth')->group(function () {
    // Note: {id} menggunakan route model binding sesuai Order::getRouteKeyName()
    Route::get('/checkout/{id}', [PaymentController::class, 'qris'])->name('payment.qris');
    Route::post('/payment/generate-qris/{id}', [PaymentController::class, 'generateQris'])->name('payment.generateQris');
});

// WEBHOOK (public) — Midtrans akan POST tanpa CSRF token
Route::post('/webhook', [PaymentController::class, 'webhook'])->name('payment.webhook');

// AUTH (Login, Register, dsb)
require __DIR__.'/auth.php';
