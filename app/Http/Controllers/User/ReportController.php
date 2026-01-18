<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReportRequest;
use App\Interfaces\ReportCategoryRepositoryInterface;
use App\Interfaces\ReportRepositoryInterface;
use App\Services\ReportPriorityScoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    private ReportRepositoryInterface $reportRepository;
    private ReportCategoryRepositoryInterface $reportCategoryRepository;
    private ReportPriorityScoringService $priorityScoringService;

    public function __construct(
        ReportRepositoryInterface $reportRepository, 
        ReportCategoryRepositoryInterface $reportCategoryRepository,
        ReportPriorityScoringService $priorityScoringService
    ) {
        $this->reportRepository = $reportRepository;
        $this->reportCategoryRepository = $reportCategoryRepository;
        $this->priorityScoringService = $priorityScoringService;
    }

    public function index(Request $request)
    {
        $status = $request->status;
        
        if($request->category) {
            $reports = $this->reportRepository->getReportsByCategories($request->category);
        } else if($status) {
            $reports = $this->reportRepository->getReportsByStatus($status);
        } else {
            $reports = $this->reportRepository->getAllReports();
        }
        
        return view('pages.app.report.index', compact('reports', 'status'));
    }

    public function myReport(Request $request)
    {
        $status = $request->status ?? 'delivered'; // Default ke 'delivered' jika tidak ada status
        $reports = $this->reportRepository->getReportsByResidentId($status);
        
        return view('pages.app.report.my-report', compact('reports', 'status'));
    }

    public function show($code)
    {
        $report = $this->reportRepository->getReportByCode($code);

        return view('pages.app.report.show', compact('report'));
    }

    public function take()
    {
        return view('pages.app.report.take');
    }

    public function preview()
    {
        return view('pages.app.report.preview');
    }

    public function create()
    {
        $categories = $this->reportCategoryRepository->getAllReportCategories();

        return view('pages.app.report.create',compact('categories'));
    }

    public function store(StoreReportRequest $request)
    {
        $data = $request->validated();

        $data['code'] = "LAPORAJA" . mt_rand(10000,99999);
        $data['resident_id'] = Auth::user()->resident->id;
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('assets/report/image', 'public');
        }

        $report = $this->reportRepository->createReport($data);
        
        // Handle priority scoring if provided by user
        if ($request->filled(['community_impact_score', 'damage_level_score', 'time_urgency_score'])) {
            $this->priorityScoringService->updateReportPriorityScore(
                $report,
                $request->category_score, // Can be null for auto-calculation based on category
                $request->community_impact_score,
                $request->damage_level_score,
                $request->time_urgency_score,
                'User-submitted priority scoring'
            );
        }

        return redirect()->route('report.success');
    }

    public function success()
    {
    return view('pages.app.report.success');
    }

}
