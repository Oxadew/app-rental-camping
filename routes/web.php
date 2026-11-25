<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KategoriAlatController;
use App\Http\Controllers\AlatCampingController;
use App\Http\Controllers\UnitAlatController;
use App\Http\Controllers\PenyewaController;
use App\Http\Controllers\TransaksiSewaController;
use App\Http\Controllers\AuthController;

// Redirect root ke dashboard atau login
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Rute Autentikasi (Pertemuan 8)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Rute Utama Aplikasi (Nanti bungkus dengan ->middleware('auth') setelah Auth siap)
Route::group([], function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Data Master (CRUD Resource)
    Route::resource('kategori', KategoriAlatController::class);
    Route::resource('alat', AlatCampingController::class);
    Route::resource('unit-alat', UnitAlatController::class);
    Route::resource('penyewa', PenyewaController::class);

    // Transaksi Penyewaan & Pengembalian (Logika Transaksi Utama)
    Route::prefix('transaksi')->name('transaksi.')->group(function () {
        Route::get('/', [TransaksiSewaController::class, 'index'])->name('index');
        Route::get('/create', [TransaksiSewaController::class, 'create'])->name('create');
        Route::post('/', [TransaksiSewaController::class, 'store'])->name('store');
        Route::get('/{transaksi}', [TransaksiSewaController::class, 'show'])->name('show');
        
        // Form & Proses Pengembalian Alat + Hitung Denda
        Route::get('/{transaksi}/pengembalian', [TransaksiSewaController::class, 'formPengembalian'])->name('pengembalian.form');
        Route::put('/{transaksi}/pengembalian', [TransaksiSewaController::class, 'prosesPengembalian'])->name('pengembalian.proses');
    });

    // Laporan
    Route::get('/laporan', [TransaksiSewaController::class, 'laporan'])->name('laporan.index');
});