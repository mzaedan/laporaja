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

class RejectedReportsSheet implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
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
        $query = Report::with(['resident.user', 'reportCategory', 'reportStatuses'])
            ->whereHas('reportStatuses', function($query) {
                $query->where('status', 'rejected')
                    ->whereIn('id', function($subquery) {
                        $subquery->selectRaw('MAX(id)')
                                ->from('report_statuses')
                                ->groupBy('report_id');
                    });
            });

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
            'Alasan Penolakan'
        ];
    }

    public function map($report): array
    {
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
            $report->reportStatuses->last()->description ?? '-',
        ];
    }

    public function title(): string
    {
        return "Laporan Ditolak";
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

        return $sheet;
    }
}
