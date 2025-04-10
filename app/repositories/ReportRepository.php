<?php

namespace App\Repositories;

use App\Interfaces\ReportRepositoryInterface;
use App\Models\Report;
use App\Models\ReportCategory;
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