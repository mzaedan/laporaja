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

        // Debug: Tampilkan rentang waktu yang digunakan
        \Log::info('Rentang waktu laporan:', [
            'dari' => $currentMonth->toDateTimeString(),
            'sampai' => $nextMonth->toDateTimeString()
        ]);

        // Query dasar untuk laporan bulan ini dengan status terakhir
        $reports = Report::select('reports.*')
            ->whereBetween('reports.created_at', [$currentMonth, $nextMonth])
            ->leftJoin('report_statuses', function($join) {
                $join->on('reports.id', '=', 'report_statuses.report_id')
                    ->whereRaw('report_statuses.id = (select max(id) from report_statuses where report_id = reports.id)');
            });

        // Debug: Tampilkan query yang dihasilkan
        \Log::info('Query dasar:', [
            'sql' => $reports->toSql(),
            'bindings' => $reports->getBindings()
        ]);

        // Total laporan pada bulan ini
        $totalReports = (clone $reports)->count();
        $completedReports = (clone $reports)->where('report_statuses.status', 'completed')->count();
        $pendingReports = (clone $reports)->where('report_statuses.status', 'in_process')->count();
        $highPriorityReports = (clone $reports)->where('report_statuses.status', 'rejected')->count();

        // Debug: Tampilkan hasil perhitungan
        \Log::info('Hasil perhitungan:', [
            'total' => $totalReports,
            'selesai' => $completedReports,
            'tertunda' => $pendingReports,
            'prioritas_tinggi' => $highPriorityReports
        ]);

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
            ->whereIn('report_statuses.status', ['completed', 'in_process', 'rejected'])
            ->groupBy('report_statuses.status')
            ->get();

        // Data untuk chart - distribusi kategori
        $categoryDistribution = Report::with('reportCategory')
            ->select(
                'report_category_id',
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('created_at', [$currentMonth, $nextMonth])
            ->groupBy('report_category_id')
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $item->reportCategory->name ?? 'Tidak ada kategori',
                    'count' => $item->count
                ];
            });

        // Laporan terbaru
        $recentReports = Report::with(['resident', 'reportCategory', 'reportStatuses' => function($query) {
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
 'category' => $report->reportCategory->name ?? 'Tidak ada kategori',
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
        $categoryDistribution = Report::with('reportCategory')
            ->select(
                'reports.report_category_id',
                DB::raw('COUNT(*) as count')
            )
            ->leftJoin('report_categories', 'reports.report_category_id', '=', 'report_categories.id')
            ->whereBetween('reports.created_at', [$currentMonth, $nextMonth])
            ->groupBy('reports.report_category_id')
            ->get()
            ->map(function($item) {
                return [
                    'category' => $item->reportCategory ? $item->reportCategory->name : 'Tidak ada kategori',
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
        $recentReports = Report::with(['resident', 'reportCategory', 'reportStatuses' => function($query) {
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
                    'category' => $report->reportCategory->name ?? 'Tidak ada kategori',
                    'created_at' => $report->created_at->format('d M Y H:i'),
                    'time_ago' => $report->created_at->diffForHumans()
                ];
            });

        return response()->json($recentReports);
    }
}
