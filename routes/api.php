<?php
use App\Http\Controllers\Api\OrderController;

Route::post('/orders', [OrderController::class, 'store']); // Untuk simpan transaksi
Route::get('/orders', [OrderController::class, 'index']); // Untuk tampil di Riwayat (Hal 4)
