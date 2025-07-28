<?php

namespace App\Repositories;

use App\Interfaces\ReportRepositoryInterface;
use App\Models\Report;
use App\Models\ReportCategory;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Repository untuk manajemen laporan dengan fitur Priority Queue dan Aging.
 *
 * Fitur utama:
 * - Penentuan prioritas otomatis berdasarkan kategori
 * - Aging: prioritas naik otomatis jika laporan lama belum diproses
 * - Override manual prioritas oleh admin/operator
 */
class ReportRepository implements ReportRepositoryInterface {
    // Konstanta untuk level prioritas
    private const PRIORITY_HIGH = 3;   // Keamanan & Keselamatan
    private const PRIORITY_MEDIUM = 2; // Infrastruktur
    private const PRIORITY_LOW = 1;    // Administrasi
    // Default aging factor (peningkatan prioritas per hari)
    private const AGING_FACTOR = 0.1;
    /**
     * Override manual prioritas laporan (misal: oleh admin/operator)
     * @param int $reportId
     * @param int $newPriority
     * @return bool
     */
    public function overrideReportPriority(int $reportId, int $newPriority): bool
    {
        $report = $this->getReportById($reportId);
        if (!$report) {
            return false;
        }
        // Update urgency_level dan simpan juga initial_urgency jika ingin tracking
        $report->urgency_level = $newPriority;
        $report->initial_urgency = $newPriority;
        return $report->save();
    }


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
            1 => self::PRIORITY_HIGH,   // Kategori Keamanan
            2 => self::PRIORITY_MEDIUM, // Kategori Infrastruktur
            3 => self::PRIORITY_LOW     // Kategori Administrasi
        ];
        
        $categoryId = $data['report_category_id'] ?? null;
        $data['urgency_level'] = $priorityMap[$categoryId] ?? self::PRIORITY_LOW;
        $data['initial_urgency'] = $data['urgency_level']; // Menyimpan prioritas awal

        $report = Report::create($data);
        $report->reportStatuses()->create([
            'status' => 'delivered',
            'description' => 'Laporan Berhasil Diterima',
        ]);

        return $report;
    }

    public function updateReport(array $data, int $id)
    {
        $report = $this->getReportById($id);

        // Mapping report_category_id ke level prioritas (sama seperti createReport)
        $priorityMap = [
            1 => self::PRIORITY_HIGH,   // Kategori Keamanan
            2 => self::PRIORITY_MEDIUM, // Kategori Infrastruktur
            3 => self::PRIORITY_LOW     // Kategori Administrasi
        ];
        if (isset($data['report_category_id'])) {
            $categoryId = $data['report_category_id'];
            $data['urgency_level'] = $priorityMap[$categoryId] ?? self::PRIORITY_LOW;
        }
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
            ->selectRaw('*, 
                LEAST(?, urgency_level + (DATEDIFF(NOW(), created_at) * ?)) as effective_priority', 
                [self::PRIORITY_HIGH, self::AGING_FACTOR])
            ->orderBy('effective_priority', 'desc')
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

    /**
     * Menghitung dan memperbarui prioritas berdasarkan waktu (aging)
     * @param Report $report
     * @return float
     */
    private function calculateEffectivePriority(Report $report): float
    {
        $daysWaiting = now()->diffInDays($report->created_at);
        $agingBonus = $daysWaiting * self::AGING_FACTOR;
        
        // Batasi maksimum prioritas sampai PRIORITY_HIGH
        return min(self::PRIORITY_HIGH, $report->urgency_level + $agingBonus);
    }

    /**
     * Memperbarui prioritas laporan dengan mempertimbangkan aging
     * @param int $reportId
     * @return bool
     */
    public function updatePriorityWithAging(int $reportId): bool
    {
        $report = $this->getReportById($reportId);
        if (!$report) {
            return false;
        }

        $effectivePriority = $this->calculateEffectivePriority($report);
        return $this->updateReportUrgency($reportId, $effectivePriority);
    }
}