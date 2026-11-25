<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KategoriAlatController;
use App\Http\Controllers\AlatCampingController;
use App\Http\Controllers\UnitAlatController;
use App\Http\Controllers\PenyewaController;
use App\Http\Controllers\TransaksiSewaController;
use App\Http\Controllers\AuthController;

// Redirect root: jika sudah login ke dashboard, jika belum otomatis dilempar ke login oleh middleware auth
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// 1. Rute Tamu (Hanya bisa diakses jika BELUM login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// 2. Rute Logout (Hanya bisa diakses jika SUDAH login)
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// 3. Rute Terproteksi (Wajib Login)
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Data Master (CRUD Resource)
    Route::resource('kategori', KategoriAlatController::class);
    Route::resource('alat', AlatCampingController::class);
    Route::resource('unit-alat', UnitAlatController::class);
    Route::resource('penyewa', PenyewaController::class);

    // Transaksi Penyewaan & Pengembalian
    Route::prefix('transaksi')->name('transaksi.')->group(function () {
        Route::get('/', [TransaksiSewaController::class, 'index'])->name('index');
        Route::get('/create', [TransaksiSewaController::class, 'create'])->name('create');
        Route::post('/', [TransaksiSewaController::class, 'store'])->name('store');
        Route::get('/{transaksi}', [TransaksiSewaController::class, 'show'])->name('show');

        Route::get('/{transaksi}/pengembalian', [TransaksiSewaController::class, 'formPengembalian'])->name('pengembalian.form');
        Route::put('/{transaksi}/pengembalian', [TransaksiSewaController::class, 'prosesPengembalian'])->name('pengembalian.proses');
    });

    // Laporan
    Route::get('/laporan', [TransaksiSewaController::class, 'laporan'])->name('laporan.index');
});