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
    
    // Test Priority Ordering in Admin Panel
    Route::get('/test-priority-ordering', function () {
        $reportRepo = app(\App\repositories\ReportRepository::class);
        $reports = $reportRepo->getReportsByPriorityScore();
        
        $results = [
            'total_reports' => $reports->count(),
            'with_priority_scoring' => $reports->filter(function($report) { 
                return $report->hasPriorityScoring(); 
            })->count(),
            'priority_distribution' => [
                'tinggi' => $reports->filter(function($report) { 
                    return $report->hasPriorityScoring() && $report->calculated_priority_score >= 3.8; 
                })->count(),
                'sedang' => $reports->filter(function($report) { 
                    return $report->hasPriorityScoring() && $report->calculated_priority_score >= 2.6 && $report->calculated_priority_score < 3.8; 
                })->count(),
                'rendah' => $reports->filter(function($report) { 
                    return $report->hasPriorityScoring() && $report->calculated_priority_score < 2.6; 
                })->count(),
                'belum_dinilai' => $reports->filter(function($report) { 
                    return !$report->hasPriorityScoring(); 
                })->count()
            ],
            'sample_reports' => $reports->take(5)->map(function($report) {
                return [
                    'code' => $report->code,
                    'title' => $report->title,
                    'category' => $report->reportCategory->name ?? 'N/A',
                    'has_priority_scoring' => $report->hasPriorityScoring(),
                    'priority_score' => $report->calculated_priority_score,
                    'priority_level' => $report->hasPriorityScoring() ? $report->priority_level : 'Belum Dinilai',
                    'effective_priority' => $report->hasPriorityScoring() ? $report->effective_priority_score : null,
                    'created_at' => $report->created_at->format('d/m/Y H:i:s')
                ];
            })->toArray()
        ];
        
        return response()->json(['success' => true, 'priority_ordering_test' => $results]);
    })->name('test.priority.ordering');
    
    // Test 3-Category Priority Scoring System
    Route::get('/test-3category-priority', function () {
        $service = app(\App\Services\ReportPriorityScoringService::class);
        
        $testResults = [
            'new_weights' => $service->getWeights(),
            'category_scoring' => [
                'keamanan_dan_keselamatan_publik' => $service->getCategoryScore('Keamanan dan Keselamatan Publik'),
                'infrastruktur_dan_fasilitas_umum' => $service->getCategoryScore('Infrastruktur dan Fasilitas Umum'),
                'administrasi_dan_layanan_umum' => $service->getCategoryScore('Administrasi dan Layanan Umum'),
            ],
            'sample_calculations' => [
                'high_priority_emergency' => [
                    'description' => 'Kebocoran Gas (Keamanan)',
                    'scores' => [5, 4, 3, 5], // kategori, komunitas, kerusakan, urgensi
                    'result' => $service->calculatePriorityScore(5, 4, 3, 5),
                    'level' => $service->getPriorityLevel($service->calculatePriorityScore(5, 4, 3, 5))
                ],
                'medium_priority_infrastructure' => [
                    'description' => 'Jalan Berlubang (Infrastruktur)',
                    'scores' => [3, 3, 3, 3],
                    'result' => $service->calculatePriorityScore(3, 3, 3, 3),
                    'level' => $service->getPriorityLevel($service->calculatePriorityScore(3, 3, 3, 3))
                ],
                'low_priority_admin' => [
                    'description' => 'Keluhan Administrasi',
                    'scores' => [2, 1, 1, 1],
                    'result' => $service->calculatePriorityScore(2, 1, 1, 1),
                    'level' => $service->getPriorityLevel($service->calculatePriorityScore(2, 1, 1, 1))
                ]
            ],
            'criteria_definitions' => $service->getCriteriaDefinitions()
        ];
        
        return response()->json(['success' => true, 'new_3category_system' => $testResults]);
    })->name('test.3category.priority');
    
    Route::get('/test-report-status-creation', function () {
        // Find a report to test with
        $report = \App\Models\Report::first();
        
        if (!$report) {
            return response()->json(['error' => 'No reports found for testing']);
        }
        
        // Test data
        $testData = [
            'report_id' => $report->id,
            'status' => 'in_process',
            'description' => 'Test progress created at ' . now()->format('Y-m-d H:i:s')
        ];
        
        try {
            // Test repository method directly
            $repository = app(\App\Interfaces\ReportStatusRepositoryInterface::class);
            $newStatus = $repository->createReportStatus($testData);
            
            return response()->json([
                'success' => true,
                'message' => 'Report status created successfully',
                'data' => $newStatus,
                'test_data' => $testData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    })->name('test.report.status.creation');
    
    Route::get('/test-report-status-form-full', function () {
        $reports = \App\Models\Report::with('reportCategory')->take(10)->get();
        return view('test-report-status', compact('reports'));
    })->name('test.report.status.form.full');
    
    Route::get('/test-report-status-form/{reportId}', function ($reportId) {
        $report = \App\Models\Report::findOrFail($reportId);
        return view('pages.admin.report-status.create', compact('report'));
    })->name('test.report.status.form');
    
    // Enhanced Priority Scoring Test Route
    Route::get('/test-report-creation', function () {
        try {
            // Find an existing report to test with
            $existingReport = \App\Models\Report::first();
            
            if (!$existingReport) {
                return response()->json([
                    'success' => false,
                    'error' => 'No existing reports found to test with'
                ]);
            }
            
            // Test updating a report with priority scoring (this was the failing operation)
            $reportRepository = app(\App\Interfaces\ReportRepositoryInterface::class);
            $priorityService = app(\App\Services\ReportPriorityScoringService::class);
            
            // Test the priority scoring update that was failing
            $result = $priorityService->updateReportPriorityScore(
                $existingReport,
                5, // category_score
                4, // community_impact_score  
                3, // damage_level_score
                2, // time_urgency_score
                'Test priority scoring update'
            );
            
            // Refresh the report to get updated data
            $existingReport->refresh();
            
            return response()->json([
                'success' => true,
                'message' => 'Report creation with priority scoring test completed',
                'update_result' => $result,
                'updated_report' => [
                    'id' => $existingReport->id,
                    'category_score' => $existingReport->category_score,
                    'calculated_priority_score' => $existingReport->calculated_priority_score,
                    'community_impact_score' => $existingReport->community_impact_score,
                    'damage_level_score' => $existingReport->damage_level_score,
                    'time_urgency_score' => $existingReport->time_urgency_score,
                    'scoring_notes' => $existingReport->scoring_notes,
                    'priority_calculated_at' => $existingReport->priority_calculated_at,
                    'priority_level' => $existingReport->priority_level,
                    'effective_priority_score' => $existingReport->effective_priority_score
                ],
                'error_resolved' => true
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'error_resolved' => false
            ]);
        }
    })->name('test.report.creation')->middleware(['auth']);
    
    Route::get('/test-priority-scoring', function () {
        try {
            $service = app(\App\Services\ReportPriorityScoringService::class);
            
            // Test priority calculation
            $testScore = $service->calculatePriorityScore(5, 4, 3, 2);
            
            // Test category scoring
            $categoryScores = [
                'keamanan' => $service->getCategoryScore('Keamanan dan Keselamatan Publik'),
                'infrastruktur' => $service->getCategoryScore('Infrastruktur dan Fasilitas Umum'),
                'administrasi' => $service->getCategoryScore('Administrasi dan Layanan Umum'),
            ];
            
            // Test priority level determination
            $priorityLevels = [
                'high' => $service->getPriorityLevel(4.5),
                'medium' => $service->getPriorityLevel(3.0),
                'low' => $service->getPriorityLevel(2.0)
            ];
            
            return response()->json([
                'success' => true,
                'message' => 'Priority scoring service test completed',
                'test_calculation' => $testScore,
                'category_scores' => $categoryScores,
                'priority_levels' => $priorityLevels,
                'weights' => $service->getWeights(),
                'service_ready' => true
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    })->name('test.priority.scoring')->middleware(['auth']);
    
    Route::get('/test-database-schema', function () {
        try {
            // Test if priority scoring columns exist
            $report = \DB::table('reports')
                ->select('id', 'category_score', 'calculated_priority_score', 'community_impact_score', 'damage_level_score', 'time_urgency_score')
                ->first();
                
            $columns = \DB::getSchemaBuilder()->getColumnListing('reports');
            $priorityColumns = ['category_score', 'calculated_priority_score', 'community_impact_score', 'damage_level_score', 'time_urgency_score', 'scoring_notes', 'priority_calculated_at'];
            
            $existingPriorityColumns = array_intersect($priorityColumns, $columns);
            $missingColumns = array_diff($priorityColumns, $columns);
            
            return response()->json([
                'success' => true,
                'message' => 'Database schema test completed',
                'all_columns' => $columns,
                'priority_columns_exist' => $existingPriorityColumns,
                'missing_columns' => $missingColumns,
                'sample_report' => $report,
                'schema_ready' => empty($missingColumns)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    })->name('test.database.schema')->middleware(['auth']);
    
    Route::get('/test-cache-busting', function () {
        return response()->json([
            'success' => true,
            'timestamp' => now()->toISOString(),
            'random' => rand(1000, 9999),
            'message' => 'Cache busting is working! This data should be fresh on every request.',
            'headers' => [
                'cache_control' => request()->header('Cache-Control'),
                'pragma' => request()->header('Pragma'),
                'expires' => request()->header('Expires')
            ]
        ]);
    })->name('test.cache.busting')->middleware(['auth', 'prevent.caching']);
    
    Route::get('/test-enhanced-priority', function () {
        $service = app(\App\Services\ReportPriorityScoringService::class);
        
        $testResults = [
            'category_5_others_3' => $service->calculatePriorityScore(5, 3, 3, 3), // Should be high due to category weight
            'category_1_others_5' => $service->calculatePriorityScore(1, 5, 5, 5), // Lower due to low category
            'balanced_high' => $service->calculatePriorityScore(4, 4, 4, 4),
            'weights' => $service->getWeights(),
            'category_auto_scoring' => [
                'keamanan' => $service->getCategoryScore('keamanan'),
                'infrastruktur' => $service->getCategoryScore('infrastruktur'),
                'administrasi' => $service->getCategoryScore('administrasi'),
            ]
        ];
        
        return response()->json(['success' => true, 'enhanced_results' => $testResults]);
    })->name('test.enhanced.priority');
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

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:Admin', 'prevent.caching'])->group(function () {
    route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    route::resource('/resident', ResidentController::class);
    route::resource('/report-category', ReportCategoryController::class);
    route::resource('/report', ReportController::class);
    route::get('/report/completed/list', [ReportController::class, 'completed'])->name('report.completed');
    route::get('/report/in-process/list', [ReportController::class, 'inProcess'])->name('report.in-process');
    route::get('/report-status/{reportId}/create', [ReportStatusController::class, 'create'])->name('report-status.create');
    route::resource('/report-status', ReportStatusController::class)->except('create');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('report.export');
});