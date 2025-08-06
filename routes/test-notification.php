<?php

use App\Events\ReportStatusUpdated;
use App\Models\ReportStatus;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

Route::get('/test-notification', function () {
    // Get the most recent report status with its relationships
    $reportStatus = ReportStatus::with(['report.resident'])->latest()->first();
    
    if (!$reportStatus) {
        return 'No report status found for testing. Please create a report status first.';
    }
    
    $userId = $reportStatus->report->resident->user_id ?? null;
    
    if (!$userId) {
        return 'No user ID found for the report owner.';
    }
    
    Log::info('Manually triggering notification', [
        'report_status_id' => $reportStatus->id,
        'report_id' => $reportStatus->report_id,
        'user_id' => $userId,
        'status' => $reportStatus->status
    ]);
    
    // Dispatch the event
    event(new ReportStatusUpdated($reportStatus, $userId));
    
    return response()->json([
        'message' => 'Test notification triggered',
        'report_id' => $reportStatus->report_id,
        'status' => $reportStatus->status,
        'user_id' => $userId
    ]);
})->middleware('web');
