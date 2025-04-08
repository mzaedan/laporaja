<?php

namespace App\Repositories;

use App\Interfaces\ReportRepositoryInterface;
use App\Models\Report;
use App\Models\User;

class ReportRepository implements ReportRepositoryInterface {

    public function getAllReports()
    {
        return Report::all();
    }

    public function getLatestReports()
    {
         return Report::latest()->take(5)->get();
    }

    public function getReportById(int $id)
    {
        return Report::where('id', $id)->first();
    }

    public function createReport(array $data)
    {
        return Report::create($data);
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
}

?>