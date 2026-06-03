<?php
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\CustomerController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/cashiers', [AuthController::class, 'cashiers']);
Route::get('/dashboard-stats', [OrderController::class, 'dashboardStats']);
Route::post('/orders', [OrderController::class, 'store']);
Route::get('/orders', [OrderController::class, 'index']);
Route::get('/services', [ServiceController::class, 'index']);
Route::post('/services', [ServiceController::class, 'store']);
Route::put('/services/reorder', [ServiceController::class, 'reorder']);
Route::put('/services/{id}', [ServiceController::class, 'update']);
Route::delete('/services/{id}', [ServiceController::class, 'destroy']);
Route::get('/customers', [CustomerController::class, 'index']);
Route::post('/customers', [CustomerController::class, 'store']);
Route::patch('/transactions/{id}/status', [App\Http\Controllers\Api\OrderController::class, 'updateStatus']);
Route::delete('/orders/{id}', [OrderController::class, 'destroy']);
Route::put('/orders/{id}/customer-info', [OrderController::class, 'updateCustomerInfo']);
