<?php

namespace App\Exports\Sheets;

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

class AllReportsSheet implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
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
            'No',
            'Kode Laporan',
            'Tanggal',
            'Pelapor',
            'Kategori',
            'Judul',
            'Deskripsi',
            'Level Urgensi',
            'Status',
            'Laporan yang ditolak',
        ];
    }

    public function map($report): array
    {
        $status = $report->reportStatuses->last()->status ?? '-';
        $statusText = match($status) {
            'delivered' => 'Terkirim',
            'in_process' => 'Diproses',
            'completed' => 'Selesai',
            'rejected' => 'Ditolak',
            default => '-'
        };

        $urgencyText = match($report->urgency_level) {
            3 => 'Tinggi',
            2 => 'Sedang',
            1 => 'Rendah',
            default => '-'
        };

        $rejectionReason = $report->reportStatuses
            ->where('status', 'rejected')
            ->last()
            ->rejection_reason ?? '-';

        static $rowNumber = 1;
        return [
            $rowNumber++,
            $report->code,
            $report->created_at->format('d/m/Y'),
            $report->resident->user->name ?? '-',
            $report->reportCategory->name ?? '-',
            $report->title,
            $report->description,
            $urgencyText,
            $statusText,
            $rejectionReason,
        ];
    }

    public function title(): string
    {
        if ($this->month && $this->year) {
            $monthName = Carbon::createFromDate($this->year, $this->month, 1)->translatedFormat('F Y');
            return "Semua Laporan $monthName";
        } elseif ($this->year) {
            return "Semua Laporan $this->year";
        } else {
            return "Semua Laporan";
        }
    }

    public function styles(Worksheet $sheet)
    {
        // Style for headers
        $sheet->getStyle('A1:J1')->applyFromArray([
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
        $sheet->getStyle('A1:J' . ($sheet->getHighestRow()))->applyFromArray([
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
        $rejectedReports = $this->collection()->filter(function($report) {
            return $report->reportStatuses->last()->status === 'rejected';
        })->count();

        // Add summary with dynamic numbering
        $summaryData = [
            ["Ringkasan:", ""],
            ["Total Laporan:", $totalReports],
            ["Laporan Selesai:", $completedReports],
            ["Laporan Tertunda:", $pendingReports],
            ["Laporan Prioritas Tinggi:", $highPriorityReports],
            ["Laporan Ditolak:", $rejectedReports]
        ];

        // Write summary data
        foreach ($summaryData as $index => $data) {
            $row = $lastRow + $index;
            $sheet->setCellValue("B$row", $data[0]);
            $sheet->setCellValue("C$row", $data[1] ?? '');
            
            // Add numbering to column A
            if ($index > 0) { // Skip number for the "Ringkasan:" row
                $sheet->setCellValue("A$row", $index);
            }
        }

        // Style the summary header
        $sheet->getStyle("B$lastRow")->getFont()->setBold(true);
        $sheet->mergeCells("B$lastRow:C$lastRow");

        // Set specific width for the numbering column
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getStyle('A')->getAlignment()->setHorizontal('center');

        return $sheet;
    }
}
