<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReportCategoryController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ReportStatusController;
use App\Http\Controllers\Admin\ResidentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\User\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware(['auth']);

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:Admin'])->group(function () {
    route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    route::resource('/resident', ResidentController::class);
    route::resource('/report-category', ReportCategoryController::class);
    route::resource('/report', ReportController::class);
    route::get('/report-status/{reportId}/create', [ReportStatusController::class, 'create'])->name('report-status.create');
    route::resource('/report-status', ReportStatusController::class)->except('create');
});