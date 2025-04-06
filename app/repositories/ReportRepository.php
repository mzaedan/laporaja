<?php

namespace App\Repositories;

use App\Interfaces\ReportRepositoryInterface;
use App\Models\ReportCategory;
use App\Models\User;

class ReportRepository implements ReportRepositoryInterface {

    public function getAllReports()
    {
        return ReportCategory::all();
    }

    public function getReportById(int $id)
    {
        return ReportCategory::where('id', $id)->first();
    }

    public function createReport(array $data)
    {
        return ReportCategory::create($data);
    }

    public function updateReport(array $data, int $id)
    {
        $reportCategory = $this->getReportById($id);
        
        return $reportCategory->update($data);
    }

    public function deleteReport(int $id)
    {
        $reportCategory = $this->getReportById($id);
        
        return $reportCategory->delete();
    }
}

?>