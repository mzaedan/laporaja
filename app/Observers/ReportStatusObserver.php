<?php

namespace App\Observers;

use App\Models\ReportStatus;
use App\Models\Report;
use App\Events\ReportStatusUpdated;

class ReportStatusObserver
{
    /**
     * Handle the ReportStatus "creating" event.
     */
    public function creating(ReportStatus $reportStatus)
    {
        \Log::info('🎬 ReportStatusObserver: creating event triggered', [
            'report_id' => $reportStatus->report_id,
            'status' => $reportStatus->status,
            'description' => $reportStatus->description
        ]);
    }
    
    /**
     * Handle the ReportStatus "created" event.
     */
    public function created(ReportStatus $reportStatus)
    {
        \Log::info('🆕 ReportStatusObserver: created event triggered', [
            'status_id' => $reportStatus->id,
            'report_id' => $reportStatus->report_id,
            'status' => $reportStatus->status,
            'timestamp' => now()
        ]);
        
        // Auto-mark as unread when new status is created
        $reportStatus->is_read = false;
        $reportStatus->saveQuietly();

        // Dispatch event for the report owner
        $this->dispatchStatusUpdatedEvent($reportStatus, 'created');
    }
    
    /**
     * Handle the ReportStatus "updating" event.
     */
    public function updating(ReportStatus $reportStatus)
    {
        \Log::info('🔄 ReportStatusObserver: updating event triggered', [
            'status_id' => $reportStatus->id,
            'report_id' => $reportStatus->report_id,
            'original' => $reportStatus->getOriginal(),
            'changes' => $reportStatus->getDirty()
        ]);
    }

    /**
     * Handle the ReportStatus "updated" event.
     */
    public function updated(ReportStatus $reportStatus)
    {
        \Log::info('🔄 ReportStatusObserver: updated event triggered', [
            'status_id' => $reportStatus->id,
            'report_id' => $reportStatus->report_id,
            'status' => $reportStatus->status,
            'changes' => $reportStatus->getChanges(),
            'dirty' => $reportStatus->getDirty(),
            'was_changed_status' => $reportStatus->wasChanged('status'),
            'timestamp' => now()
        ]);
        
        // If status changed, mark as unread and dispatch event
        if ($reportStatus->wasChanged('status')) {
            \Log::info('✅ Status field changed, dispatching event');
            $reportStatus->is_read = false;
            $reportStatus->saveQuietly();
            
            // Dispatch event for the report owner
            $this->dispatchStatusUpdatedEvent($reportStatus, 'updated');
        } else {
            \Log::info('⏸️ Status field not changed, skipping event dispatch');
        }
    }
    
    /**
     * Dispatch the ReportStatusUpdated event
     */
    protected function dispatchStatusUpdatedEvent(ReportStatus $reportStatus, string $eventType = 'unknown')
    {
        try {
            \Log::info('🚀 Starting event dispatch process', [
                'status_id' => $reportStatus->id,
                'report_id' => $reportStatus->report_id,
                'event_type' => $eventType
            ]);
            
            // Load the report with the resident relationship if not already loaded
            if (!$reportStatus->relationLoaded('report')) {
                \Log::info('📦 Loading report relationship');
                $reportStatus->load('report.resident');
            }
            
            // Check if report exists
            if (!$reportStatus->report) {
                \Log::error('❌ Report not found for ReportStatus', [
                    'status_id' => $reportStatus->id,
                    'report_id' => $reportStatus->report_id
                ]);
                return;
            }
            
            // Check if resident exists
            if (!$reportStatus->report->resident) {
                \Log::error('❌ Resident not found for Report', [
                    'report_id' => $reportStatus->report_id,
                    'resident_id' => $reportStatus->report->resident_id ?? 'null'
                ]);
                return;
            }
            
            // Get the user ID from the report's resident
            $userId = $reportStatus->report->resident->user_id ?? null;
            
            if ($userId) {
                \Log::info('✅ Dispatching ReportStatusUpdated event', [
                    'report_id' => $reportStatus->report_id,
                    'status_id' => $reportStatus->id,
                    'status' => $reportStatus->status,
                    'user_id' => $userId,
                    'resident_id' => $reportStatus->report->resident_id ?? null,
                    'channel' => 'report-status.' . $userId,
                    'event_type' => $eventType
                ]);
                
                // Dispatch the event
                $event = new ReportStatusUpdated($reportStatus, $userId);
                event($event);
                
                \Log::info('🎉 Event dispatched successfully');
            } else {
                \Log::warning('⚠️ Cannot dispatch ReportStatusUpdated: No user ID found for resident', [
                    'report_id' => $reportStatus->report_id,
                    'status_id' => $reportStatus->id,
                    'resident_id' => $reportStatus->report->resident_id ?? null,
                    'resident_exists' => $reportStatus->report->resident ? 'yes' : 'no',
                    'resident_user_id' => $reportStatus->report->resident->user_id ?? 'null'
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('❌ Error in dispatchStatusUpdatedEvent: ' . $e->getMessage(), [
                'report_id' => $reportStatus->report_id ?? null,
                'status_id' => $reportStatus->id ?? null,
                'exception' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
