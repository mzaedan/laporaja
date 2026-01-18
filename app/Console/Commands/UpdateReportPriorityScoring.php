<?php

namespace App\Console\Commands;

use App\Services\ReportPriorityScoringService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateReportPriorityScoring extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:update-priority-scoring {--force : Force update all reports including those with existing scores}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bulk update priority scoring for reports using new 3-category system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting bulk update of report priority scoring...');
        
        $force = $this->option('force');
        $service = app(ReportPriorityScoringService::class);
        
        try {
            DB::beginTransaction();
            
            if ($force) {
                $this->warn('Force mode enabled - updating ALL reports');
                $updated = $this->updateAllReports($service);
            } else {
                $updated = $service->bulkCalculateExistingReports();
            }
            
            DB::commit();
            
            $this->info("Successfully updated priority scoring for {$updated} reports.");
            
            // Show summary
            $this->showSummary();
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error updating priority scoring: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
    
    /**
     * Update all reports (force mode)
     */
    private function updateAllReports(ReportPriorityScoringService $service): int
    {
        $reports = \App\Models\Report::all();
        $updated = 0;
        
        $this->withProgressBar($reports, function ($report) use ($service, &$updated) {
            // Use urgency_level to derive default scoring
            $urgencyLevel = $report->urgency_level ?? 1;
            
            // Get category score automatically from report category
            $categoryScore = $service->getCategoryScore($report->reportCategory ?? 'umum');
            
            // Map urgency to scoring criteria
            $communityImpact = min(5, max(1, $urgencyLevel));
            $damageLevel = min(5, max(1, $urgencyLevel));
            $timeUrgency = min(5, max(1, $urgencyLevel));
            
            $service->updateReportPriorityScore(
                $report,
                $categoryScore,
                $communityImpact,
                $damageLevel,
                $timeUrgency,
                'Bulk update berdasarkan kategori dan urgency_level'
            );
            
            $updated++;
        });
        
        $this->newLine();
        return $updated;
    }
    
    /**
     * Show summary of priority scoring distribution
     */
    private function showSummary()
    {
        $this->newLine();
        $this->info('Priority Scoring Summary:');
        
        $service = app(ReportPriorityScoringService::class);
        
        $counts = [
            'Tinggi (≥3.8)' => \App\Models\Report::where('calculated_priority_score', '>=', 3.8)->count(),
            'Sedang (2.6-3.7)' => \App\Models\Report::whereBetween('calculated_priority_score', [2.6, 3.79])->count(),
            'Rendah (<2.6)' => \App\Models\Report::where('calculated_priority_score', '<', 2.6)
                                                    ->where('calculated_priority_score', '>', 0)->count(),
            'Belum Dinilai' => \App\Models\Report::where('calculated_priority_score', 0)
                                                 ->orWhereNull('calculated_priority_score')->count(),
        ];
        
        $this->table(
            ['Priority Level', 'Count'],
            collect($counts)->map(fn($count, $level) => [$level, $count])->toArray()
        );
        
        $totalWithScoring = \App\Models\Report::where('calculated_priority_score', '>', 0)->count();
        $totalReports = \App\Models\Report::count();
        
        $this->info("Total reports with priority scoring: {$totalWithScoring}/{$totalReports}");
    }
}
