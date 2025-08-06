<?php

namespace App\Observers;

use App\Models\ReportStatus;
use App\Models\Report;
use App\Events\ReportStatusUpdated;

class ReportStatusObserver
{
    /**
     * Handle the ReportStatus "created" event.
     */
    public function created(ReportStatus $reportStatus)
    {
        // Auto-mark as unread when new status is created
        $reportStatus->is_read = false;
        $reportStatus->saveQuietly();

        // Dispatch event for the report owner
        $this->dispatchStatusUpdatedEvent($reportStatus);
    }

    /**
     * Handle the ReportStatus "updated" event.
     */
    public function updated(ReportStatus $reportStatus)
    {
        // If status changed, mark as unread and dispatch event
        if ($reportStatus->wasChanged('status')) {
            $reportStatus->is_read = false;
            $reportStatus->saveQuietly();
            
            // Dispatch event for the report owner
            $this->dispatchStatusUpdatedEvent($reportStatus);
        }
    }
    
    /**
     * Dispatch the ReportStatusUpdated event
     */
    protected function dispatchStatusUpdatedEvent(ReportStatus $reportStatus)
    {
        try {
            // Load the report with the resident relationship if not already loaded
            if (!$reportStatus->relationLoaded('report')) {
                $reportStatus->load('report.resident');
            }
            
            // Get the user ID from the report's resident
            $userId = $reportStatus->report->resident->user_id ?? null;
            
            if ($userId) {
                \Log::info('Dispatching ReportStatusUpdated event', [
                    'report_id' => $reportStatus->report_id,
                    'status_id' => $reportStatus->id,
                    'status' => $reportStatus->status,
                    'user_id' => $userId,
                    'resident_id' => $reportStatus->report->resident_id ?? null
                ]);
                
                event(new ReportStatusUpdated($reportStatus, $userId));
            } else {
                \Log::warning('Cannot dispatch ReportStatusUpdated: No user ID found for resident', [
                    'report_id' => $reportStatus->report_id,
                    'status_id' => $reportStatus->id,
                    'resident_id' => $reportStatus->report->resident_id ?? null
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Error in dispatchStatusUpdatedEvent: ' . $e->getMessage(), [
                'report_id' => $reportStatus->report_id ?? null,
                'status_id' => $reportStatus->id ?? null,
                'exception' => $e
            ]);
        }
    }
}
