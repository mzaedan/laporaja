<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReportCategoryController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ReportStatusController;
use App\Http\Controllers\Admin\ResidentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\NotificationController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\ReportController as UserReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/reports',[UserReportController::class, 'index'])->name('report.index');
Route::get('/report/{code}', [UserReportController::class, 'show'])->name('report.show');

Route::middleware(['auth'])->group(function(){
    // Route detail laporan admin
    Route::get('/admin/laporan/{id}', [\App\Http\Controllers\Admin\DashboardController::class, 'show'])->name('admin.laporan.show');
    Route::get('/take-report',[UserReportController::class, 'take'])->name('report.take');
    Route::get('/preview',[UserReportController::class, 'preview'])->name('report.preview');
    Route::get('/create-report',[UserReportController::class, 'create'])->name('report.create');
    Route::post('/create-report',[UserReportController::class, 'store'])->name('report.store');
    Route::get('/report-success', [UserReportController::class, 'success'])->name('report.success');
    Route::get('/my-report',[UserReportController::class, 'myReport'])->name('report.myreport');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
    
    Route::get('profile/',[ProfileController::class, 'index'])->name('profile');
    
    // Perbaikan: Konsistensi parameter dan pindahkan feedback.success ke dalam auth middleware
    Route::get('/feedback/{report}', [\App\Http\Controllers\FeedbackController::class, 'form'])->name('feedback.form');
    Route::post('/feedback/{report}', [\App\Http\Controllers\FeedbackController::class, 'store'])->name('feedback.store');
    Route::get('/feedback-success', [\App\Http\Controllers\FeedbackController::class, 'success'])->name('feedback.success');
    
    Route::get('/app/notifications', function () {
        return view('pages.app.notifications');
    })->name('app.notifications');
});

Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware(['auth']);

Route::get('/register', [RegisterController::class, 'index'])->name('register');
Route::post('/register',[RegisterController::class, 'store'])->name('register.store');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:Admin'])->group(function () {
    route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    route::resource('/resident', ResidentController::class);
    route::resource('/report-category', ReportCategoryController::class);
    route::resource('/report', ReportController::class);
    route::get('/report-status/{reportId}/create', [ReportStatusController::class, 'create'])->name('report-status.create');
    route::resource('/report-status', ReportStatusController::class)->except('create');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('report.export');
});