<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil laporan yang:
        // - status terakhirnya 'in_process' atau 'delivered' lebih dari 2 hari
        // - ATAU termasuk kategori urgent (report_category_id = 1)
        // - TIDAK termasuk status 'completed'
        $notifikasi = \App\Models\Report::where(function($query) {
            $query->whereHas('latestReportStatus', function($q) {
                $q->whereIn('status', ['in_process', 'delivered'])
                  ->whereDate('created_at', '<', now()->subDays(2));
            })
            ->orWhere('report_category_id', 1);
        })
        ->whereHas('latestReportStatus', function($q) {
            $q->whereNotIn('status', ['completed']);
        })
        ->with(['latestReportStatus'])
        ->get();

        return view('pages.admin.dashboard', compact('notifikasi'));
    }

    public function show($id)
    {
        $laporan = \App\Models\Report::with(['latestReportStatus', 'reportCategory', 'resident'])->findOrFail($id);
        return view('pages.admin.laporan-detail', compact('laporan'));
    }
}
