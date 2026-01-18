<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReportRequest;
use App\Http\Requests\UpdateReportRequest;
use App\Interfaces\ReportCategoryRepositoryInterface;
use App\Interfaces\ReportRepositoryInterface;
use App\Interfaces\ResidentRepositoryInterface;
use App\Services\ReportPriorityScoringService;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert as Swal;
use App\Exports\ReportsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;

class ReportController extends Controller
{
    private ReportRepositoryInterface $reportRepository;
    private ReportCategoryRepositoryInterface $reportCategoryRepository;
    private ResidentRepositoryInterface $residentRepository;
    private ReportPriorityScoringService $priorityScoringService;

    public function __construct(
        ReportRepositoryInterface $reportRepository,
        ReportCategoryRepositoryInterface $reportCategoryRepository,
        ResidentRepositoryInterface $residentRepository,
        ReportPriorityScoringService $priorityScoringService
    ) {
        $this->reportRepository = $reportRepository;
        $this->reportCategoryRepository = $reportCategoryRepository;
        $this->residentRepository = $residentRepository;
        $this->priorityScoringService = $priorityScoringService;
    }


    /**
     * Display a listing of the resource (only delivered reports).
     */
    public function index()
    {
        // Clear cache to ensure fresh data
        \Cache::flush();
        
        // Get reports with 'delivered' status ordered by effective priority score
        $reports = $this->reportRepository->getReportsByPriorityScore(['status' => 'delivered']);

        return view('pages.admin.report.index', compact('reports'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $residents = $this->residentRepository->getAllResidents();
        $categories = $this->reportCategoryRepository->getAllReportCategories();

        return view('pages.admin.report.create', compact('residents', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReportRequest $request)
    {
        $data = $request->validated();

        $data['code'] = "LAPORAJA" . mt_rand(10000,99999);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('assets/report/image', 'public');
        }

        $report = $this->reportRepository->createReport($data);
        
        // Handle priority scoring if provided
        if ($request->filled(['community_impact_score', 'damage_level_score', 'time_urgency_score'])) {
            $this->priorityScoringService->updateReportPriorityScore(
                $report,
                $request->category_score, // Can be null for auto-calculation
                $request->community_impact_score,
                $request->damage_level_score,
                $request->time_urgency_score,
                $request->scoring_notes
            );
        }

        Swal::toast('Data Laporan Berhasil Ditambahkan', 'success')->timerProgressBar();

        return redirect()->route('admin.report.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        // Force fresh data retrieval by clearing any potential caches
        \Cache::flush();
        
        $report = $this->reportRepository->getReportById($id);
        
        // Add timestamp to ensure browser doesn't cache this view
        $timestamp = time();

        return view('pages.admin.report.show', compact('report', 'timestamp'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $report = $this->reportRepository->getReportById($id);

        $residents = $this->residentRepository->getAllResidents();
        $categories = $this->reportCategoryRepository->getAllReportCategories();

        return view('pages.admin.report.edit', compact('report', 'residents', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReportRequest $request, string $id)
    {
        $data =  $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('assets/report/image', 'public');
        }

        $this->reportRepository->updateReport($data, $id);
        
        // Handle priority scoring if provided
        if ($request->filled(['community_impact_score', 'damage_level_score', 'time_urgency_score'])) {
            $report = $this->reportRepository->getReportById($id);
            $this->priorityScoringService->updateReportPriorityScore(
                $report,
                $request->category_score, // Can be null for auto-calculation
                $request->community_impact_score,
                $request->damage_level_score,
                $request->time_urgency_score,
                $request->scoring_notes
            );
        }

        Swal::toast('Data Laporan Berhasil Diupdate', 'success')->timerProgressBar();

        return redirect()->route('admin.report.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->reportRepository->deleteReport($id);

        Swal::toast('Data Laporan Berhasil Dihapus', 'success')->timerProgressBar();

        return redirect()->route('admin.report.index');
    }

    /**
     * Export the specified resource to an Excel file.
     */
    public function export(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');

        $filename = 'laporan-pengaduan-';
        if ($month && $year) {
            $filename .= $month.'-'.$year;
        } elseif ($year) {
            $filename .= $year;
        } else {
            $filename .= 'semua-periode';
        }
        $filename .= '.xlsx';

        return Excel::download(new ReportsExport($month, $year), $filename);
    }

    /**
     * Display a listing of completed reports.
     */
    public function completed()
    {
        // Clear cache to ensure fresh data
        \Cache::flush();
        
        // Get reports with 'completed' and 'rejected' status ordered by effective priority score
        $completedReports = $this->reportRepository->getReportsByPriorityScore(['status' => 'completed']);
        $rejectedReports = $this->reportRepository->getReportsByPriorityScore(['status' => 'rejected']);
        
        // Combine both collections and sort by effective priority
        $reports = $completedReports->merge($rejectedReports)
            ->sortByDesc(function($report) {
                return $report->hasPriorityScoring() ? $report->effective_priority_score : 0;
            })
            ->values(); // Reset array keys
            
        return view('pages.admin.report.completed', compact('reports'));
    }

    /**
     * Display a listing of in-process reports.
     */
    public function inProcess()
    {
        // Clear cache to ensure fresh data
        \Cache::flush();
        
        // Get reports with 'in_process' status ordered by effective priority score
        $reports = $this->reportRepository->getReportsByPriorityScore(['status' => 'in_process']);
        return view('pages.admin.report.in-process', compact('reports'));
    }
}
