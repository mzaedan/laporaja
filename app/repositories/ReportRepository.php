<?php

namespace App\Repositories;

use App\Interfaces\ReportRepositoryInterface;
use App\Models\Report;
use App\Models\ReportCategory;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ReportRepository implements ReportRepositoryInterface {

    public function getAllReports()
    {
        return Report::all();
    }

    public function getLatestReports()
    {
        return Report::latest()->take(5)->get();
    }

   public function getReportsByResidentId(string $status)
    {
        $resident = Auth::user()->resident;

        return Report::where('resident_id', $resident->id)
            ->whereHas('reportStatuses', function($query) use ($status) {
                $query->where('status', $status)
                    ->whereIn('id', function($subquery) {
                        $subquery->selectRaw('MAX(id)')
                                ->from('report_statuses')
                                ->groupBy('report_id');
                    });
            })
            ->get();
    }

    public function getReportById(int $id)
    {
        return Report::with(['feedbacks.user', 'reportStatuses'])->where('id', $id)->first();
    }

    public function getReportByCode(string $code)
    {
        return Report::where('code', $code)->first();
    }

    public function getReportsByCategories(string $category)
    {
        $category = ReportCategory::where('name', $category)->first();

        return Report::where('report_category_id',$category->id)->get();
    }

    public function createReport(array $data)
    {
        // Mapping report_category_id ke level prioritas
        $priorityMap = [
            1 => 2, // Sedang
            2 => 3, // Tinggi
            3 => 1, // Rendah
        ];
        $categoryId = $data['report_category_id'] ?? null;
        $urgency = $priorityMap[$categoryId] ?? 1; // Default ke 'Rendah' jika tidak ditemukan
        $data['urgency_level'] = $urgency;

        $report = Report::create($data);
        $report->reportStatuses()->create([
            'status' => 'delivered',
            'description' => 'laporan Berhasil Diterima',
        ]);
    }

    public function updateReport(array $data, int $id)
    {
        $report = $this->getReportById($id);
        
        return $report->update($data);
    }

    public function deleteReport(int $id)
    {
        $report = $this->getReportById($id);
        
        return $report->delete();
    }

    public function getPrioritizedReports()
    {
        return Report::whereHas('reportStatuses', function($query) {
                $query->whereIn('id', function($subquery) {
                    $subquery->selectRaw('MAX(id)')
                            ->from('report_statuses')
                            ->where('status', '!=', 'selesai')
                            ->groupBy('report_id');
                });
            })
            ->orderBy('urgency_level', 'desc')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function updateReportUrgency(int $reportId, int $urgencyLevel)
    {
        $report = $this->getReportById($reportId);
        if ($report) {
            $report->update([
                'urgency_level' => $urgencyLevel
            ]);
            return true;
       }
        return false;
    }
}