<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil laporan yang status terakhirnya 'in_process' atau 'delivered' lebih dari 3 hari
        $notifikasi = \App\Models\Report::whereHas('latestReportStatus', function($q) {
            $q->whereIn('status', ['in_process', 'delivered'])
              ->whereDate('created_at', '<', now()->subDays(3));
        })->with(['latestReportStatus'])->get();

        return view('pages.admin.dashboard', compact('notifikasi'));
    }
}
