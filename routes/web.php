<?php

use App\Http\Controllers\Admin\DashboardController;

// Include test routes
if (app()->environment('local')) {
    require __DIR__.'/test-pusher.php';
    require __DIR__.'/test-notification.php';
    require __DIR__.'/debug-config.php';
    
    // Debug routes
    Route::get('/pusher-debug', function () {
        return view('debug.pusher-test');
    })->name('pusher.debug')->middleware('auth');
    
    Route::get('/test-notifikasi', function () {
        return view('test-notification');
    })->name('test.notification')->middleware('auth');
    
    Route::get('/test-pusher-complete', function () {
        return view('test-pusher-complete');
    })->name('test.pusher.complete')->middleware('auth');
    
    Route::get('/trigger-notification', function () {
        if (!auth()->check()) {
            return response()->json(['error' => 'Not authenticated']);
        }
        
        // Find a report status for the current user or create a fake one
        $userId = auth()->id();
        
        // Try to find an existing report status
        $reportStatus = \App\Models\ReportStatus::whereHas('report.resident', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })->with('report')->first();
        
        if (!$reportStatus) {
            return response()->json([
                'error' => 'No report status found for current user',
                'suggestion' => 'Create a report first, then try again'
            ]);
        }
        
        // Dispatch the event
        event(new \App\Events\ReportStatusUpdated($reportStatus, $userId));
        
        return response()->json([
            'success' => true,
            'message' => 'Notification event triggered successfully',
            'report_id' => $reportStatus->report_id,
            'status' => $reportStatus->status,
            'user_id' => $userId
        ]);
    })->name('trigger.notification')->middleware('auth');
    
    Route::get('/test-observer', function () {
        if (!auth()->check()) {
            return response()->json(['error' => 'Not authenticated']);
        }
        
        $userId = auth()->id();
        
        // Find an existing report status for the user
        $reportStatus = \App\Models\ReportStatus::whereHas('report.resident', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })->first();
        
        if (!$reportStatus) {
            return response()->json([
                'error' => 'No report status found for current user',
                'suggestion' => 'Create a report first, then try again'
            ]);
        }
        
        // Test the observer by updating the status using repository method
        $repository = app(\App\Interfaces\ReportStatusRepositoryInterface::class);
        
        // Update with a different status to trigger the observer
        $newStatus = $reportStatus->status === 'in_progress' ? 'completed' : 'in_progress';
        
        \Log::info('Testing observer by updating report status', [
            'status_id' => $reportStatus->id,
            'old_status' => $reportStatus->status,
            'new_status' => $newStatus,
            'user_id' => $userId
        ]);
        
        // This should trigger the observer
        $result = $repository->updateReportStatus(['status' => $newStatus], $reportStatus->id);
        
        return response()->json([
            'success' => true,
            'message' => 'Observer test completed - check logs',
            'status_id' => $reportStatus->id,
            'old_status' => $reportStatus->status,
            'new_status' => $newStatus,
            'update_result' => $result,
            'note' => 'Check Laravel logs for observer activity'
        ]);
    })->name('test.observer')->middleware('auth');
    
    Route::get('/create-real-status', function () {
        if (!auth()->check()) {
            return response()->json(['error' => 'Not authenticated']);
        }
        
        $userId = auth()->id();
        
        // Find a report for the current user
        $report = \App\Models\Report::whereHas('resident', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })->first();
        
        if (!$report) {
            return response()->json([
                'error' => 'No report found for current user',
                'suggestion' => 'Create a report first, then try again'
            ]);
        }
        
        // Create a new report status using the repository (same as admin)
        $repository = app(\App\Interfaces\ReportStatusRepositoryInterface::class);
        
        $statusData = [
            'report_id' => $report->id,
            'status' => 'in_process',
            'description' => 'Test status created via debug route at ' . now()->format('H:i:s')
        ];
        
        \Log::info('Creating new report status via repository', $statusData);
        
        // This should trigger the observer's created method
        $newStatus = $repository->createReportStatus($statusData);
        
        return response()->json([
            'success' => true,
            'message' => 'New report status created - check logs and notifications',
            'report_id' => $report->id,
            'new_status_id' => $newStatus->id,
            'status' => $newStatus->status,
            'user_id' => $userId,
            'note' => 'Check Laravel logs for observer activity and browser for notifications'
        ]);
    })->name('create.real.status')->middleware('auth');
}
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
use App\Http\Controllers\HelpController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/reports',[UserReportController::class, 'index'])->name('report.index');
Route::get('/report/{code}', [UserReportController::class, 'show'])->name('report.show');
Route::get('/help', [HelpController::class, 'index'])->name('help');

// Public Dashboard Routes
Route::get('/dashboard', [\App\Http\Controllers\PublicDashboardController::class, 'index'])->name('public.dashboard');
Route::get('/api/dashboard-data', [\App\Http\Controllers\PublicDashboardController::class, 'getDashboardData'])->name('api.dashboard.data');
Route::get('/api/recent-reports', [\App\Http\Controllers\PublicDashboardController::class, 'getRecentReports'])->name('api.recent.reports');

// API Routes for notifications
Route::get('/api/unread-notification-count', function () {
    if (!auth()->check()) {
        return response()->json(['count' => 0]);
    }
    
    $userId = auth()->id();
    $count = \App\Models\ReportStatus::whereHas('report.resident', function($query) use ($userId) {
        $query->where('user_id', $userId);
    })->where('is_read', false)->count();
    
    return response()->json(['count' => $count]);
})->name('api.unread.notifications');

Route::post('/api/mark-notifications-read', function () {
    if (!auth()->check()) {
        return response()->json(['success' => false]);
    }
    
    $userId = auth()->id();
    \App\Models\ReportStatus::whereHas('report.resident', function($query) use ($userId) {
        $query->where('user_id', $userId);
    })->where('is_read', false)->update(['is_read' => true]);
    
    return response()->json(['success' => true]);
})->name('api.mark.notifications.read');

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
    Route::post('profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');
    
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
    route::get('/report/completed/list', [ReportController::class, 'completed'])->name('report.completed');
    route::get('/report-status/{reportId}/create', [ReportStatusController::class, 'create'])->name('report-status.create');
    route::resource('/report-status', ReportStatusController::class)->except('create');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('report.export');
});