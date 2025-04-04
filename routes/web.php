<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:Admin'])->group(function () {
    route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});