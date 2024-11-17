<?php

use App\Http\Controllers\AppController;
// use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\Logistics\LogisticsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/{any}', [AppController::class, 'index'])->where('any', '.*');

// Auth::routes();

Route::post('/password-reset-request', [PasswordResetController::class, 'requestPasswordReset'])->name('password.reset.request');
Route::post('/password-reset-upload', [PasswordResetController::class, 'uploadFile'])->name('password.reset.upload');

Route::middleware('auth')->prefix('logistics')->name('logistics.')->group(function () {
    Route::get('paid-orders/', [LogisticsController::class, 'paidOrders'])->name('paidOrders');
});
