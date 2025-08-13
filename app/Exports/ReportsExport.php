<?php

namespace App\Exports;

use App\Exports\Sheets\AllReportsSheet;
use App\Exports\Sheets\RejectedReportsSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ReportsExport implements WithMultipleSheets
{
    protected $month;
    protected $year;

    public function __construct($month = null, $year = null)
    {
        $this->month = $month;
        $this->year = $year;
    }

    public function sheets(): array
    {
        return [
            new AllReportsSheet($this->month, $this->year),
            new RejectedReportsSheet($this->month, $this->year)
        ];
    }
}
