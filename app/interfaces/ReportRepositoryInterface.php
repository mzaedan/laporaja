<?php 

namespace App\Interfaces;


interface ReportRepositoryInterface {

    public function getAllReports();

    public function getLatestReports();

    public function getReportsByResidentId(string $status);

    public function getReportById(int $id);

    public function getReportByCode(string $code);

    public function getReportsByCategories(string $category);
    
    public function getReportsByStatus(string $status);

    public function createReport(array $data);

    public function updateReport(array $data, int $id);

    public function deleteReport(int $id);

    public function getPrioritizedReports();

    public function updateReportUrgency(int $reportId, int $urgencyLevel);

    public function getCompletedReports();

    public function getInProcessReports();
}

?>