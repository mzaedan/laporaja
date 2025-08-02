<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicDashboardController extends Controller
{
    public function index()
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $nextMonth = Carbon::now()->addMonth()->startOfMonth();

        // Total laporan pada bulan ini
        $totalReports = Report::whereBetween('created_at', [$currentMonth, $nextMonth])
            ->count();

        // Laporan selesai pada bulan ini
        $completedReports = Report::whereBetween('reports.created_at', [$currentMonth, $nextMonth])
            ->join('report_statuses', function($join) {
                $join->on('reports.id', '=', 'report_statuses.report_id')
                    ->whereRaw('report_statuses.id = (select max(id) from report_statuses where report_id = reports.id)');
            })
            ->where('report_statuses.status', 'selesai')
            ->count();

        // Laporan tertunda pada bulan ini
        $pendingReports = Report::whereBetween('reports.created_at', [$currentMonth, $nextMonth])
            ->join('report_statuses', function($join) {
                $join->on('reports.id', '=', 'report_statuses.report_id')
                    ->whereRaw('report_statuses.id = (select max(id) from report_statuses where report_id = reports.id)');
            })
            ->where('report_statuses.status', 'tertunda')
            ->count();

        // Laporan dengan prioritas tinggi pada bulan ini
        $highPriorityReports = Report::whereBetween('created_at', [$currentMonth, $nextMonth])
            ->where('urgency_level', 'tinggi')
            ->count();

        // Data untuk chart - laporan per hari
        $dailyReports = Report::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('created_at', [$currentMonth, $nextMonth])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Data untuk chart - distribusi status
        $statusDistribution = Report::select(
                'report_statuses.status',
                DB::raw('COUNT(*) as count')
            )
            ->join('report_statuses', function($join) {
                $join->on('reports.id', '=', 'report_statuses.report_id')
                    ->whereRaw('report_statuses.id = (select max(id) from report_statuses where report_id = reports.id)');
            })
            ->whereBetween('reports.created_at', [$currentMonth, $nextMonth])
            ->groupBy('report_statuses.status')
            ->get();

        // Data untuk chart - distribusi kategori
        $categoryDistribution = Report::with('category')
            ->select(
                DB::raw('category_id'),
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('created_at', [$currentMonth, $nextMonth])
            ->groupBy('category_id')
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $item->category->name ?? 'Tidak ada kategori',
                    'count' => $item->count
                ];
            });

        // Laporan terbaru
        $recentReports = Report::with(['user', 'category', 'reportStatuses' => function($query) {
                $query->latest()->limit(1);
            }])
            ->whereHas('reportStatuses', function($query) {
                $query->where('status', '!=', 'draft');
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($report) {
                $latestStatus = $report->reportStatuses->first();
                return [
                    'id' => $report->id,
                    'title' => $report->title,
                    'status' => $latestStatus ? $latestStatus->status : null,
                    'priority' => $report->urgency_level,
                    'category' => $report->category->name ?? 'Tidak ada kategori',
                    'code' => $report->code,
                    'time_ago' => $report->created_at->diffForHumans()
                ];
            });

        return view('pages.public.dashboard', compact(
            'totalReports',
            'completedReports',
            'pendingReports',
            'highPriorityReports',
            'dailyReports',
            'statusDistribution',
            'categoryDistribution',
            'recentReports'
        ));
    }

    public function getDashboardData()
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $nextMonth = Carbon::now()->addMonth()->startOfMonth();

        // Total laporan pada bulan ini
        $totalReports = Report::whereBetween('created_at', [$currentMonth, $nextMonth])
            ->count();

        // Laporan selesai pada bulan ini
        $completedReports = Report::whereBetween('reports.created_at', [$currentMonth, $nextMonth])
            ->join('report_statuses', function($join) {
                $join->on('reports.id', '=', 'report_statuses.report_id')
                    ->whereRaw('report_statuses.id = (select max(id) from report_statuses where report_id = reports.id)');
            })
            ->where('report_statuses.status', 'selesai')
            ->count();

        // Laporan tertunda pada bulan ini
        $pendingReports = Report::whereBetween('reports.created_at', [$currentMonth, $nextMonth])
            ->join('report_statuses', function($join) {
                $join->on('reports.id', '=', 'report_statuses.report_id')
                    ->whereRaw('report_statuses.id = (select max(id) from report_statuses where report_id = reports.id)');
            })
            ->where('report_statuses.status', 'tertunda')
            ->count();

        // Laporan dengan prioritas tinggi pada bulan ini
        $highPriorityReports = Report::whereBetween('created_at', [$currentMonth, $nextMonth])
            ->where('urgency_level', 'tinggi')
            ->count();

        // Data untuk chart - laporan per hari
        $dailyReports = Report::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
            ->whereBetween('created_at', [$currentMonth, $nextMonth])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Data untuk chart - laporan berdasarkan status
        $statusDistribution = Report::select(
            'report_statuses.status',
            DB::raw('COUNT(*) as count')
        )
            ->join('report_statuses', function($join) {
                $join->on('reports.id', '=', 'report_statuses.report_id')
                    ->whereRaw('report_statuses.id = (select max(id) from report_statuses where report_id = reports.id)');
            })
            ->whereBetween('reports.created_at', [$currentMonth, $nextMonth])
            ->groupBy('report_statuses.status')
            ->get();

        // Data untuk chart - laporan berdasarkan kategori
        $categoryDistribution = Report::with('category')
            ->select(
                'reports.category_id',
                DB::raw('COUNT(*) as count')
            )
            ->leftJoin('report_categories', 'reports.category_id', '=', 'report_categories.id')
            ->whereBetween('reports.created_at', [$currentMonth, $nextMonth])
            ->groupBy('reports.category_id')
            ->get()
            ->map(function($item) {
                return [
                    'category' => $item->category ? $item->category->name : 'Tidak ada kategori',
                    'count' => $item->count
                ];
            });

        return response()->json([
            'total_reports' => $totalReports,
            'completed_reports' => $completedReports,
            'pending_reports' => $pendingReports,
            'high_priority_reports' => $highPriorityReports,
            'daily_reports' => $dailyReports,
            'status_distribution' => $statusDistribution,
            'category_distribution' => $categoryDistribution,
            'current_month' => Carbon::now()->format('F Y')
        ]);
    }

    public function getRecentReports()
    {
        $recentReports = Report::with(['user', 'category', 'reportStatuses' => function($query) {
                $query->latest()->limit(1);
            }])
            ->whereHas('reportStatuses', function($query) {
                $query->where('status', '!=', 'draft');
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($report) {
                $latestStatus = $report->reportStatuses->first();
                return [
                    'id' => $report->id,
                    'title' => $report->title,
                    'status' => $latestStatus ? $latestStatus->status : null,
                    'priority' => $report->urgency_level,
                    'category' => $report->category->name ?? 'Tidak ada kategori',
                    'created_at' => $report->created_at->format('d M Y H:i'),
                    'time_ago' => $report->created_at->diffForHumans()
                ];
            });

        return response()->json($recentReports);
    }
}
