<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ReportStatus;
use App\Models\Report;
use App\Interfaces\ReportStatusRepositoryInterface;

class TestReportStatusObserver extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:observer {action=create : Action to perform (create|update)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test ReportStatus observer by creating or updating report statuses';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        
        $this->info('🧪 Testing ReportStatus Observer...');
        $this->info('Action: ' . $action);
        
        if ($action === 'create') {
            return $this->testCreate();
        } elseif ($action === 'update') {
            return $this->testUpdate();
        } else {
            $this->error('Invalid action. Use "create" or "update"');
            return 1;
        }
    }
    
    private function testCreate()
    {
        // Find a report to attach the status to
        $report = Report::with('resident')->first();
        
        if (!$report) {
            $this->error('❌ No reports found in database');
            return 1;
        }
        
        if (!$report->resident) {
            $this->error('❌ Report has no resident');
            return 1;
        }
        
        if (!$report->resident->user_id) {
            $this->error('❌ Resident has no user_id');
            return 1;
        }
        
        $this->info('📊 Report found:');
        $this->line('  Report ID: ' . $report->id);
        $this->line('  Report Code: ' . $report->code);
        $this->line('  Resident ID: ' . $report->resident->id);
        $this->line('  User ID: ' . $report->resident->user_id);
        
        // Create a new report status using repository
        $repository = app(ReportStatusRepositoryInterface::class);
        
        $statusData = [
            'report_id' => $report->id,
            'status' => 'in_process',
            'description' => 'Test status created via console at ' . now()->format('Y-m-d H:i:s')
        ];
        
        $this->info('🆕 Creating new report status...');
        $this->line('Data: ' . json_encode($statusData, JSON_PRETTY_PRINT));
        
        try {
            $newStatus = $repository->createReportStatus($statusData);
            $this->info('✅ Report status created successfully!');
            $this->line('  Status ID: ' . $newStatus->id);
            $this->line('  Status: ' . $newStatus->status);
            $this->info('📋 Check Laravel logs for observer activity');
            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Failed to create report status: ' . $e->getMessage());
            return 1;
        }
    }
    
    private function testUpdate()
    {
        // Find an existing report status
        $reportStatus = ReportStatus::with(['report.resident'])->first();
        
        if (!$reportStatus) {
            $this->error('❌ No report statuses found in database');
            return 1;
        }
        
        if (!$reportStatus->report) {
            $this->error('❌ Report status has no report');
            return 1;
        }
        
        if (!$reportStatus->report->resident) {
            $this->error('❌ Report has no resident');
            return 1;
        }
        
        if (!$reportStatus->report->resident->user_id) {
            $this->error('❌ Resident has no user_id');
            return 1;
        }
        
        $this->info('📊 Report status found:');
        $this->line('  Status ID: ' . $reportStatus->id);
        $this->line('  Report ID: ' . $reportStatus->report_id);
        $this->line('  Current Status: ' . $reportStatus->status);
        $this->line('  User ID: ' . $reportStatus->report->resident->user_id);
        
        // Update the status using repository
        $repository = app(ReportStatusRepositoryInterface::class);
        
        $newStatus = $reportStatus->status === 'in_process' ? 'completed' : 'in_process';
        $updateData = [
            'status' => $newStatus,
            'description' => $reportStatus->description . ' [Updated via console at ' . now()->format('Y-m-d H:i:s') . ']'
        ];
        
        $this->info('🔄 Updating report status...');
        $this->line('Old Status: ' . $reportStatus->status);
        $this->line('New Status: ' . $newStatus);
        
        try {
            $result = $repository->updateReportStatus($updateData, $reportStatus->id);
            $this->info('✅ Report status updated successfully!');
            $this->line('Update Result: ' . ($result ? 'true' : 'false'));
            $this->info('📋 Check Laravel logs for observer activity');
            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Failed to update report status: ' . $e->getMessage());
            return 1;
        }
    }
}
