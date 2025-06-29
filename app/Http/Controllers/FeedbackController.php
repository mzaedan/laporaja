<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    public function store(Request $request, Report $report)
    {
        // Pastikan hanya pemilik laporan yang bisa memberi feedback
        if ($report->resident_id !== Auth::user()->resident->id) {
            abort(403);
        }
        
        // Pastikan status laporan sudah selesai
        if (!$report->reportStatuses()->where('status', 'completed')->exists()) {
            return back()->with('error', 'Feedback hanya bisa diberikan jika laporan sudah selesai.');
        }
        
        // Pastikan belum ada feedback
        if ($report->feedback) {
            return back()->with('error', 'Feedback sudah pernah dikirim.');
        }
        
        $validated = $request->validate([
            'impression' => 'required|string|max:1000',
            'message' => 'required|string|max:1000',
            'satisfaction' => 'required|integer|min:1|max:5',
        ]);
        
        Feedback::create([
            'report_id' => $report->id,
            'user_id' => Auth::id(),
            'impression' => $validated['impression'],
            'message' => $validated['message'],
            'satisfaction' => $validated['satisfaction'],
        ]);
        
        return redirect()->route('feedback.success');
    }

    // Perbaikan: Konsistensi parameter - menggunakan Report model binding
    public function form(Report $report)
    {
        // Pastikan hanya pemilik laporan yang bisa memberi feedback
        if ($report->resident_id !== auth()->user()->resident->id) {
            abort(403);
        }
        
        // Pastikan status laporan sudah selesai
        if (!$report->reportStatuses()->where('status', 'completed')->exists()) {
            return back()->with('error', 'Feedback hanya bisa diberikan jika laporan sudah selesai.');
        }
        
        // Pastikan belum ada feedback
        if ($report->feedback) {
            return back()->with('error', 'Feedback sudah pernah dikirim.');
        }
        
        return view('pages.feedback_form', compact('report'));
    }

    public function success()
    {
        return view('pages.feedback_success');
    }
}