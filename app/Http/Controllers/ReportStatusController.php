<?php

namespace App\Http\Controllers;

use App\Models\ReportStatus;
use Illuminate\Http\Request;
use App\Notifications\ReportStatusCreated;

class ReportStatusController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            // ...validation rules...
        ]);

        // Create a new report_status
        $reportStatus = ReportStatus::create($validatedData);

        // Notify the user associated with the report
        $user = $reportStatus->report->user;
        if ($user) {
            $user->notify(new ReportStatusCreated($reportStatus));
        }

        // Return a response, such as redirecting to a specific route
        return redirect()->route('report_status.index')->with('success', 'Report status created and user notified.');
    }

    // ...existing methods...
}