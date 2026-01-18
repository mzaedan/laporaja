<?php

namespace App\Services;

use App\Models\Report;
use Carbon\Carbon;

class ReportPriorityScoringService
{
    /**
     * Priority scoring criteria weights
     */
    const WEIGHTS = [
        'category' => 0.40,         // 40% - Report category importance
        'community_impact' => 0.30, // 30% - Community impact (affected residents)
        'damage_level' => 0.20,     // 20% - Infrastructure damage scale
        'time_urgency' => 0.10,     // 10% - Time urgency
    ];

    /**
     * Category priority mapping for 3 main categories
     * Maps report category names to priority scores (1-5)
     */
    const CATEGORY_PRIORITY_MAP = [
        // High Priority Categories (Score 5)
        'keamanan_dan_keselamatan_publik' => 5,
        'keamanan' => 5,
        'keselamatan' => 5,
        'keamanan dan keselamatan publik' => 5,
        
        // Medium Priority (Score 3)
        'infrastruktur_dan_fasilitas_umum' => 3,
        'infrastruktur' => 3,
        'fasilitas_umum' => 3,
        'infrastruktur dan fasilitas umum' => 3,
        
        // Low Priority (Score 2)
        'administrasi_dan_layanan_umum' => 2,
        'administrasi' => 2,
        'layanan_umum' => 2,
        'administrasi dan layanan umum' => 2,
    ];

    /**
     * Scoring criteria definitions for 3-category system
     */
    const CRITERIA_DEFINITIONS = [
        'category' => [
            5 => 'Keamanan dan Keselamatan Publik - Kebakaran, kebocoran gas, kecelakaan, tindak kriminal, struktur roboh',
            3 => 'Infrastruktur dan Fasilitas Umum - Jalan rusak, lampu jalan, drainase, air bersih, listrik',
            2 => 'Administrasi dan Layanan Umum - Pelayanan dokumen, perizinan, keluhan pelayanan publik'
        ],
        'community_impact' => [
            1 => 'Dampak minimal - Individual atau keluarga tunggal (<10 orang)',
            2 => 'Dampak rendah - Beberapa keluarga (10-50 orang)',
            3 => 'Dampak sedang - Satu blok atau lingkungan kecil (50-200 orang)',
            4 => 'Dampak tinggi - Beberapa blok atau area (200-500 orang)',
            5 => 'Dampak sangat tinggi - Seluruh RT/komunitas besar (>500 orang)'
        ],
        'damage_level' => [
            1 => 'Maintenance/preventif - Perawatan rutin atau pencegahan',
            2 => 'Kerusakan ringan - Perlu perbaikan minor',
            3 => 'Kerusakan sedang - Masih bisa digunakan tapi perlu perbaikan',
            4 => 'Kerusakan parah - Perlu perbaikan major, tidak bisa digunakan',
            5 => 'Kerusakan total - Perlu penggantian atau rekonstruksi penuh'
        ],
        'time_urgency' => [
            1 => 'Tidak mendesak - Dapat ditangani dalam >1 bulan',
            2 => 'Urgensi rendah - Perlu ditangani dalam 2-4 minggu',
            3 => 'Urgensi sedang - Perlu ditangani dalam 2-7 hari',
            4 => 'Urgensi tinggi - Perlu ditangani dalam 24 jam',
            5 => 'Sangat mendesak - Perlu ditangani segera (dalam beberapa jam)'
        ]
    ];

    /**
     * Calculate priority score for a report
     *
     * @param int $categoryScore
     * @param int $communityImpact
     * @param int $damageLevel
     * @param int $timeUrgency
     * @return float
     */
    public function calculatePriorityScore(int $categoryScore, int $communityImpact, int $damageLevel, int $timeUrgency): float
    {
        // Validate input ranges (1-5)
        $categoryScore = max(1, min(5, $categoryScore));
        $communityImpact = max(1, min(5, $communityImpact));
        $damageLevel = max(1, min(5, $damageLevel));
        $timeUrgency = max(1, min(5, $timeUrgency));

        // Calculate weighted score
        $score = ($categoryScore * self::WEIGHTS['category']) +
                 ($communityImpact * self::WEIGHTS['community_impact']) +
                 ($damageLevel * self::WEIGHTS['damage_level']) +
                 ($timeUrgency * self::WEIGHTS['time_urgency']);

        // Round to 2 decimal places
        return round($score, 2);
    }

    /**
     * Get category score based on report category
     *
     * @param \App\Models\ReportCategory|int|string $category
     * @return int
     */
    public function getCategoryScore($category): int
    {
        if (is_object($category) && isset($category->name)) {
            $categoryName = strtolower($category->name);
        } elseif (is_string($category)) {
            $categoryName = strtolower($category);
        } else {
            // If category ID is provided, try to get name from database
            $categoryModel = \App\Models\ReportCategory::find($category);
            $categoryName = $categoryModel ? strtolower($categoryModel->name) : 'umum';
        }

        // Clean category name for mapping
        $categoryName = str_replace([' ', '-'], '_', $categoryName);
        
        // Check direct mapping first
        if (isset(self::CATEGORY_PRIORITY_MAP[$categoryName])) {
            return self::CATEGORY_PRIORITY_MAP[$categoryName];
        }
        
        // Check partial matches for common category patterns
        foreach (self::CATEGORY_PRIORITY_MAP as $key => $score) {
            if (str_contains($categoryName, $key) || str_contains($key, $categoryName)) {
                return $score;
            }
        }
        
        // Default to medium priority if no match found
        return 3;
    }

    /**
     * Update priority score for a report
     *
     * @param Report $report
     * @param int|null $categoryScore
     * @param int $communityImpact
     * @param int $damageLevel
     * @param int $timeUrgency
     * @param string|null $notes
     * @return Report
     */
    public function updateReportPriorityScore(
        Report $report,
        ?int $categoryScore,
        int $communityImpact,
        int $damageLevel,
        int $timeUrgency,
        ?string $notes = null
    ): Report {
        // If category score not provided, calculate from report category
        if ($categoryScore === null) {
            $categoryScore = $this->getCategoryScore($report->reportCategory);
        }
        
        $priorityScore = $this->calculatePriorityScore($categoryScore, $communityImpact, $damageLevel, $timeUrgency);

        $report->update([
            'category_score' => $categoryScore,
            'community_impact_score' => $communityImpact,
            'damage_level_score' => $damageLevel,
            'time_urgency_score' => $timeUrgency,
            'calculated_priority_score' => $priorityScore,
            'scoring_notes' => $notes,
            'priority_calculated_at' => Carbon::now(),
        ]);

        return $report->fresh();
    }

    /**
     * Get priority level label based on score (3-category system)
     *
     * @param float $score
     * @return string
     */
    public function getPriorityLevel(float $score): string
    {
        if ($score >= 3.8) {
            return 'Tinggi';
        } elseif ($score >= 2.6) {
            return 'Sedang';
        } else {
            return 'Rendah';
        }
    }

    /**
     * Get priority level color for UI (3-category system)
     *
     * @param float $score
     * @return string
     */
    public function getPriorityColor(float $score): string
    {
        if ($score >= 3.8) {
            return 'red';     // High Priority
        } elseif ($score >= 2.6) {
            return 'yellow';  // Medium Priority
        } else {
            return 'green';   // Low Priority
        }
    }

    /**
     * Get Bootstrap badge class for priority level
     *
     * @param float $score
     * @return string
     */
    public function getPriorityBadgeClass(float $score): string
    {
        if ($score >= 3.8) {
            return 'badge-danger';   // High Priority - Red
        } elseif ($score >= 2.6) {
            return 'badge-warning';  // Medium Priority - Yellow
        } else {
            return 'badge-success';  // Low Priority - Green
        }
    }

    /**
     * Get all criteria definitions
     *
     * @return array
     */
    public function getCriteriaDefinitions(): array
    {
        return self::CRITERIA_DEFINITIONS;
    }

    /**
     * Get criteria definition for specific type and score
     *
     * @param string $type
     * @param int $score
     * @return string|null
     */
    public function getCriteriaDefinition(string $type, int $score): ?string
    {
        return self::CRITERIA_DEFINITIONS[$type][$score] ?? null;
    }

    /**
     * Get scoring weights
     *
     * @return array
     */
    public function getWeights(): array
    {
        return self::WEIGHTS;
    }

    /**
     * Calculate aging factor for time-based priority adjustment
     *
     * @param Carbon $createdAt
     * @param float $currentScore
     * @return float
     */
    public function calculateAgingPriority(Carbon $createdAt, float $currentScore): float
    {
        $daysOld = $createdAt->diffInDays(Carbon::now());
        
        // Add 0.1 points per week (every 7 days), max addition of 1.0 point
        $agingBonus = min(1.0, ($daysOld / 7) * 0.1);
        
        // Ensure score doesn't exceed maximum of 5.0
        return min(5.0, $currentScore + $agingBonus);
    }

    /**
     * Get reports ordered by effective priority (including aging)
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function addPriorityOrderByQuery($query)
    {
        return $query->selectRaw(
            '*, 
            LEAST(5.0, calculated_priority_score + (DATEDIFF(NOW(), created_at) / 7 * 0.1)) as effective_priority_score'
        )->orderByDesc('effective_priority_score');
    }

    /**
     * Bulk calculate priority scores for existing reports
     *
     * @return int Number of reports updated
     */
    public function bulkCalculateExistingReports(): int
    {
        $updated = 0;
        $reports = Report::with('reportCategory')
                        ->whereNull('calculated_priority_score')
                        ->orWhere('calculated_priority_score', 0)
                        ->get();

        foreach ($reports as $report) {
            // Get category score automatically
            $categoryScore = $this->getCategoryScore($report->reportCategory);
            
            // Default scoring for existing reports based on urgency_level and category
            $communityImpact = min(5, max(1, $report->urgency_level ?? 1));
            $damageLevel = min(5, max(1, $report->urgency_level ?? 1));
            $timeUrgency = min(5, max(1, $report->urgency_level ?? 1));

            $this->updateReportPriorityScore(
                $report,
                $categoryScore,
                $communityImpact,
                $damageLevel,
                $timeUrgency,
                'Default scoring berdasarkan kategori dan urgency_level existing'
            );

            $updated++;
        }

        return $updated;
    }
}