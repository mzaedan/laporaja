<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateReportPriorityScoringRequest;
use App\Models\Report;
use App\Repositories\ReportRepository;
use App\Services\ReportPriorityScoringService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class PriorityScoringController extends Controller
{
    protected ReportPriorityScoringService $priorityService;
    protected ReportRepository $reportRepository;

    public function __construct(
        ReportPriorityScoringService $priorityService,
        ReportRepository $reportRepository
    ) {
        $this->priorityService = $priorityService;
        $this->reportRepository = $reportRepository;
    }

    /**
     * Get priority scoring criteria definitions
     */
    public function getCriteriaDefinitions(): JsonResponse
    {
        try {
            $definitions = $this->priorityService->getCriteriaDefinitions();
            $weights = $this->priorityService->getWeights();

            return response()->json([
                'success' => true,
                'data' => [
                    'criteria_definitions' => $definitions,
                    'weights' => $weights,
                    'score_range' => [1, 5],
                    'priority_levels' => [
                        'tinggi' => ['min' => 3.8, 'max' => 5.0, 'color' => 'red'],
                        'sedang' => ['min' => 2.6, 'max' => 3.7, 'color' => 'yellow'],
                        'rendah' => ['min' => 1.6, 'max' => 2.5, 'color' => 'green']
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting criteria definitions: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving criteria definitions'
            ], 500);
        }
    }

    /**
     * Update priority scoring for a report
     */
    public function updateReportScoring(UpdateReportPriorityScoringRequest $request, int $reportId): JsonResponse
    {
        try {
            $validated = $request->validated();

            $report = Report::findOrFail($reportId);

            $success = $this->reportRepository->updateReportPriorityScoring(
                $reportId,
                $validated['community_impact_score'],
                $validated['damage_level_score'],
                $validated['time_urgency_score'],
                $validated['scoring_notes'] ?? null
            );

            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update priority scoring'
                ], 400);
            }

            $updatedReport = Report::with(['reportCategory', 'resident.user'])
                                  ->find($reportId);

            return response()->json([
                'success' => true,
                'message' => 'Priority scoring updated successfully',
                'data' => [
                    'report' => $updatedReport,
                    'priority_level' => $updatedReport->priority_level,
                    'priority_color' => $updatedReport->priority_color,
                    'effective_priority_score' => $updatedReport->effective_priority_score,
                    'scoring_breakdown' => $updatedReport->scoring_breakdown
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Report not found'
            ], 404);
            
        } catch (\Exception $e) {
            Log::error('Error updating report scoring: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating priority scoring'
            ], 500);
        }
    }

    /**
     * Get reports with priority filtering and sorting
     */
    public function getReportsByPriority(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'priority_level' => 'nullable|string|in:tinggi,sedang,rendah',
                'status' => 'nullable|string|in:delivered,in_process,completed,rejected',
                'category_id' => 'nullable|integer|exists:report_categories,id',
                'per_page' => 'nullable|integer|min:1|max:100',
                'page' => 'nullable|integer|min:1'
            ]);

            $filters = array_filter([
                'priority_level' => $validated['priority_level'] ?? null,
                'status' => $validated['status'] ?? null,
                'category_id' => $validated['category_id'] ?? null
            ]);

            $reports = $this->reportRepository->getReportsByPriorityScore($filters);

            // Add computed attributes
            $reports->transform(function ($report) {
                $report->priority_level = $report->priority_level;
                $report->priority_color = $report->priority_color;
                $report->effective_priority_score = $report->effective_priority_score;
                $report->has_priority_scoring = $report->hasPriorityScoring();
                return $report;
            });

            // Paginate if requested
            $perPage = $validated['per_page'] ?? 15;
            $page = $validated['page'] ?? 1;
            
            $paginatedReports = $reports->forPage($page, $perPage);
            $total = $reports->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'reports' => $paginatedReports->values(),
                    'pagination' => [
                        'total' => $total,
                        'per_page' => $perPage,
                        'current_page' => $page,
                        'last_page' => ceil($total / $perPage),
                        'from' => ($page - 1) * $perPage + 1,
                        'to' => min($page * $perPage, $total)
                    ],
                    'filters_applied' => $filters
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Error getting reports by priority: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving reports'
            ], 500);
        }
    }

    /**
     * Calculate priority score preview without saving
     */
    public function calculateScorePreview(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'community_impact_score' => 'required|integer|min:1|max:5',
                'damage_level_score' => 'required|integer|min:1|max:5',
                'time_urgency_score' => 'required|integer|min:1|max:5'
            ]);

            $score = $this->priorityService->calculatePriorityScore(
                $validated['community_impact_score'],
                $validated['damage_level_score'],
                $validated['time_urgency_score']
            );

            $priorityLevel = $this->priorityService->getPriorityLevel($score);
            $priorityColor = $this->priorityService->getPriorityColor($score);

            return response()->json([
                'success' => true,
                'data' => [
                    'calculated_score' => $score,
                    'priority_level' => $priorityLevel,
                    'priority_color' => $priorityColor,
                    'breakdown' => [
                        'community_impact' => [
                            'score' => $validated['community_impact_score'],
                            'weight' => 0.4,
                            'weighted_score' => $validated['community_impact_score'] * 0.4
                        ],
                        'damage_level' => [
                            'score' => $validated['damage_level_score'],
                            'weight' => 0.3,
                            'weighted_score' => $validated['damage_level_score'] * 0.3
                        ],
                        'time_urgency' => [
                            'score' => $validated['time_urgency_score'],
                            'weight' => 0.3,
                            'weighted_score' => $validated['time_urgency_score'] * 0.3
                        ]
                    ]
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Error calculating score preview: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error calculating score preview'
            ], 500);
        }
    }

    /**
     * Get priority scoring statistics
     */
    public function getPriorityStatistics(): JsonResponse
    {
        try {
            $stats = [
                'total_reports' => Report::count(),
                'reports_with_scoring' => Report::where('calculated_priority_score', '>', 0)->count(),
                'priority_distribution' => [
                    'kritis' => Report::where('calculated_priority_score', '>=', 4.5)->count(),
                    'tinggi' => Report::whereBetween('calculated_priority_score', [3.5, 4.49])->count(),
                    'sedang' => Report::whereBetween('calculated_priority_score', [2.5, 3.49])->count(),
                    'rendah' => Report::whereBetween('calculated_priority_score', [1.5, 2.49])->count(),
                    'minimal' => Report::where('calculated_priority_score', '<', 1.5)
                                     ->where('calculated_priority_score', '>', 0)->count()
                ],
                'average_scores' => [
                    'overall' => Report::where('calculated_priority_score', '>', 0)
                                       ->avg('calculated_priority_score'),
                    'community_impact' => Report::where('community_impact_score', '>', 0)
                                                ->avg('community_impact_score'),
                    'damage_level' => Report::where('damage_level_score', '>', 0)
                                            ->avg('damage_level_score'),
                    'time_urgency' => Report::where('time_urgency_score', '>', 0)
                                            ->avg('time_urgency_score')
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting priority statistics: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving statistics'
            ], 500);
        }
    }

    /**
     * Bulk update priority scoring
     */
    public function bulkUpdateScoring(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'force' => 'nullable|boolean'
            ]);

            $force = $validated['force'] ?? false;
            
            if ($force) {
                // Force update all reports
                $service = app(\App\Services\ReportPriorityScoringService::class);
                $reports = Report::all();
                $updated = 0;
                
                foreach ($reports as $report) {
                    $urgencyLevel = $report->urgency_level ?? 1;
                    $communityImpact = min(5, max(1, $urgencyLevel));
                    $damageLevel = min(5, max(1, $urgencyLevel));
                    $timeUrgency = min(5, max(1, $urgencyLevel));
                    
                    $service->updateReportPriorityScore(
                        $report,
                        $communityImpact,
                        $damageLevel,
                        $timeUrgency,
                        'Bulk API update berdasarkan urgency_level'
                    );
                    
                    $updated++;
                }
            } else {
                // Only update reports without scoring
                $updated = $this->priorityService->bulkCalculateExistingReports();
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully updated priority scoring for {$updated} reports",
                'data' => [
                    'updated_count' => $updated,
                    'force_mode' => $force
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error in bulk update: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error in bulk update'
            ], 500);
        }
    }
}
