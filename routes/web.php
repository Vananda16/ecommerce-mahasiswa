<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Buyer;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Seller;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Publik (tanpa login): katalog produk
|--------------------------------------------------------------------------
*/
Route::get('/', [CatalogController::class, 'index'])->name('home');
Route::get('/products/{product}', [CatalogController::class, 'show'])->name('products.show');

/*
|--------------------------------------------------------------------------
| Autentikasi
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Area login (semua role)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'not.blocked'])->group(function () {
    // Arahkan ke halaman utama sesuai role
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

    // Profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Chat & pengaduan: penjual dan pembeli
    Route::middleware('role:seller,buyer')->group(function () {
        Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
        Route::get('/chat/{user}', [ChatController::class, 'show'])->name('chat.show');
        Route::post('/chat/{user}', [ChatController::class, 'store'])->name('chat.store');

        Route::get('/complaints', [ComplaintController::class, 'index'])->name('complaints.index');
        Route::post('/complaints', [ComplaintController::class, 'store'])->name('complaints.store');
    });

    /*
    |----------------------------------------------------------------------
    | PEMBELI
    |----------------------------------------------------------------------
    */
    Route::middleware('role:buyer')->group(function () {
        Route::get('/cart', [Buyer\CartController::class, 'index'])->name('cart.index');
        Route::post('/cart/{product}', [Buyer\CartController::class, 'store'])->name('cart.store');
        Route::patch('/cart/items/{item}', [Buyer\CartController::class, 'update'])->name('cart.update');
        Route::delete('/cart/items/{item}', [Buyer\CartController::class, 'destroy'])->name('cart.destroy');

        Route::get('/orders', [Buyer\OrderController::class, 'index'])->name('orders.index');
        Route::post('/orders', [Buyer\OrderController::class, 'store'])->name('orders.store');
        Route::get('/orders/{order}', [Buyer\OrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/pay', [Buyer\OrderController::class, 'pay'])->name('orders.pay');
        Route::post('/orders/{order}/cancel', [Buyer\OrderController::class, 'cancel'])->name('orders.cancel');
        Route::post('/orders/{order}/review', [Buyer\ReviewController::class, 'store'])->name('reviews.store');
    });

    /*
    |----------------------------------------------------------------------
    | PENJUAL
    |----------------------------------------------------------------------
    */
    Route::prefix('seller')->name('seller.')->middleware('role:seller')->group(function () {
        Route::get('/', [Seller\DashboardController::class, 'index'])->name('dashboard');

        Route::get('/store', [Seller\StoreController::class, 'edit'])->name('store.edit');
        Route::put('/store', [Seller\StoreController::class, 'update'])->name('store.update');

        Route::resource('products', Seller\ProductController::class)->except('show');

        Route::get('/orders', [Seller\OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [Seller\OrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/accept', [Seller\OrderController::class, 'accept'])->name('orders.accept');
        Route::post('/orders/{order}/reject', [Seller\OrderController::class, 'reject'])->name('orders.reject');
        Route::post('/orders/{order}/advance', [Seller\OrderController::class, 'advance'])->name('orders.advance');
        Route::post('/orders/{order}/payment/confirm', [Seller\OrderController::class, 'confirmPayment'])->name('orders.payment.confirm');
        Route::post('/orders/{order}/payment/reject', [Seller\OrderController::class, 'rejectPayment'])->name('orders.payment.reject');

        Route::get('/sales', [Seller\SalesController::class, 'index'])->name('sales.index');
        Route::get('/reviews', [Seller\ReviewController::class, 'index'])->name('reviews.index');
    });

    /*
    |----------------------------------------------------------------------
    | ADMIN
    |----------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

        Route::resource('users', Admin\UserController::class)->except('show');
        Route::post('/users/{user}/block', [Admin\UserController::class, 'block'])->name('users.block');
        Route::post('/users/{user}/unblock', [Admin\UserController::class, 'unblock'])->name('users.unblock');

        Route::get('/products', [Admin\ProductController::class, 'index'])->name('products.index');
        Route::post('/products/{product}/approve', [Admin\ProductController::class, 'approve'])->name('products.approve');
        Route::post('/products/{product}/reject', [Admin\ProductController::class, 'reject'])->name('products.reject');
        Route::delete('/products/{product}', [Admin\ProductController::class, 'destroy'])->name('products.destroy');

        Route::resource('categories', Admin\CategoryController::class)->only(['index', 'store', 'update', 'destroy']);

        Route::resource('transactions', Admin\TransactionController::class)->only(['index', 'show', 'update']);

        Route::get('/activity', [Admin\ActivityController::class, 'index'])->name('activity.index');

        Route::resource('complaints', Admin\ComplaintController::class)->only(['index', 'show', 'update']);

        Route::get('/sales-report', [Admin\SalesReportController::class, 'index'])->name('sales-report.index');

        Route::resource('announcements', Admin\AnnouncementController::class)->only(['index', 'store', 'update', 'destroy']);
    });
});
