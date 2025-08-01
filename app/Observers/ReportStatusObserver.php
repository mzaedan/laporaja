<?php

namespace App\Observers;

use App\Models\ReportStatus;
use App\Models\Report;

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
    }

    /**
     * Handle the ReportStatus "updated" event.
     */
    public function updated(ReportStatus $reportStatus)
    {
        // If status changed, mark as unread
        if ($reportStatus->wasChanged('status')) {
            $reportStatus->is_read = false;
            $reportStatus->saveQuietly();
        }
    }
}
