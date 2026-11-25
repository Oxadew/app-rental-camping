<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RentalApiController;

Route::get('/alat', [RentalApiController::class, 'indexAlat']);
Route::get('/alat/{id}', [RentalApiController::class, 'showAlat']);

Route::get('/transaksi', [RentalApiController::class, 'indexTransaksi']);
Route::post('/transaksi', [RentalApiController::class, 'storeTransaksi']);