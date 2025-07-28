<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ReportStatus;

class NotificationController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $statuses = collect();
        $jumlahProgres = 0;
        if ($user && $user->resident) {
            $statuses = ReportStatus::whereHas('report', function($q) use ($user) {
                $q->where('resident_id', $user->resident->id);
            })->latest()->get();
            // Hitung jumlah laporan yang statusnya belum completed
            $jumlahProgres = \App\Models\ReportStatus::whereHas('report', function($q) use ($user) {
                $q->where('resident_id', $user->resident->id);
            })->where('status', '!=', 'completed')->count();
        }
        return view('pages.notifications', compact('statuses', 'jumlahProgres'));
    }
}
