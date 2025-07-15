<?php

namespace App\Exports;

use App\Models\Report;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ReportsExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    protected $month;
    protected $year;

    public function __construct($month = null, $year = null)
    {
        $this->month = $month;
        $this->year = $year;
    }

    public function collection()
    {
        $query = Report::with(['resident.user', 'reportCategory', 'reportStatuses']);
        if ($this->month) {
            $query->whereMonth('created_at', $this->month);
        }
        if ($this->year) {
            $query->whereYear('created_at', $this->year);
        }
        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Kode Laporan',
            'Tanggal',
            'Pelapor',
            'Kategori',
            'Judul',
            'Deskripsi',
            'Level Urgensi',
            'Status',
        ];
    }

    public function map($report): array
    {
        $status = $report->reportStatuses->last()->status ?? '-';
        $statusText = match($status) {
            'delivered' => 'Terkirim',
            'in_process' => 'Diproses',
            'completed' => 'Selesai',
            default => '-'
        };

        $urgencyText = match($report->urgency_level) {
            3 => 'Tinggi',
            2 => 'Sedang',
            1 => 'Rendah',
            default => '-'
        };

        return [
            $report->code,
            $report->created_at->format('d/m/Y'),
            $report->resident->user->name ?? '-',
            $report->reportCategory->name ?? '-',
            $report->title,
            $report->description,
            $urgencyText,
            $statusText,
        ];
    }

    public function title(): string
    {
        if ($this->month && $this->year) {
            $monthName = Carbon::createFromDate($this->year, $this->month, 1)->translatedFormat('F Y');
            return "Laporan Bulanan $monthName";
        } elseif ($this->year) {
            return "Laporan Tahun $this->year";
        } else {
            return "Laporan Semua Periode";
        }
    }

    public function styles(Worksheet $sheet)
    {
        // Style for headers
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4e73df'],
            ],
        ]);

        // Add borders to all cells
        $sheet->getStyle('A1:H' . ($sheet->getHighestRow()))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        // Add summary at the bottom
        $lastRow = $sheet->getHighestRow() + 2;
        
        // Get summary data
        $totalReports = $this->collection()->count();
        $completedReports = $this->collection()->filter(function($report) {
            return $report->reportStatuses->last()->status === 'completed';
        })->count();
        $pendingReports = $totalReports - $completedReports;
        $highPriorityReports = $this->collection()->where('urgency_level', 3)->count();

        // Add summary
        $sheet->setCellValue("A$lastRow", "Ringkasan:");
        $sheet->setCellValue("A" . ($lastRow + 1), "Total Laporan:");
        $sheet->setCellValue("B" . ($lastRow + 1), $totalReports);
        $sheet->setCellValue("A" . ($lastRow + 2), "Laporan Selesai:");
        $sheet->setCellValue("B" . ($lastRow + 2), $completedReports);
        $sheet->setCellValue("A" . ($lastRow + 3), "Laporan Tertunda:");
        $sheet->setCellValue("B" . ($lastRow + 3), $pendingReports);
        $sheet->setCellValue("A" . ($lastRow + 4), "Laporan Prioritas Tinggi:");
        $sheet->setCellValue("B" . ($lastRow + 4), $highPriorityReports);

        $sheet->getStyle("A$lastRow")->getFont()->setBold(true);

        return $sheet;
    }
}
