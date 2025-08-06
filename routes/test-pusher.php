<?php

use App\Events\ReportStatusUpdated;
use App\Models\ReportStatus;
use Illuminate\Support\Facades\Route;

Route::get('/test-pusher', function () {
    // Get a report status for testing
    $reportStatus = ReportStatus::with('report.resident')->first();
    
    if (!$reportStatus) {
        return 'No report status found for testing. Please create a report status first.';
    }
    
    // Get the user ID from the report's resident
    $userId = $reportStatus->report->resident->user_id ?? null;
    
    if (!$userId) {
        return 'No user ID found for the report owner.';
    }
    
    // Dispatch the event
    event(new ReportStatusUpdated($reportStatus, $userId));
    
    return 'Test event dispatched for user ID: ' . $userId . ' and report ID: ' . $reportStatus->report_id;
})->middleware('web');
