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
        if ($user && $user->resident) {
            $statuses = ReportStatus::whereHas('report', function($q) use ($user) {
                $q->where('resident_id', $user->resident->id);
            })->latest()->get();
        }
        return view('pages.notifications', compact('statuses'));
    }
}
