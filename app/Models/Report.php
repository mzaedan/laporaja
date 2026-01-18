<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\ReportPriorityScoringService;
use Carbon\Carbon;

class Report extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'code',
        'resident_id',
        'report_category_id',
        'title',
        'description',
        'image',
        'latitude',
        'longitude',
        'address',
        'urgency_level',
        'category_score',
        'community_impact_score',
        'damage_level_score',
        'time_urgency_score',
        'calculated_priority_score',
        'scoring_notes',
        'priority_calculated_at'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'priority_calculated_at' => 'datetime',
        'calculated_priority_score' => 'decimal:2',
        'category_score' => 'integer',
        'community_impact_score' => 'integer',
        'damage_level_score' => 'integer', 
        'time_urgency_score' => 'integer',
    ];

    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }

    public function reportCategory()
    {
        return $this->belongsTo(ReportCategory::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function reportStatuses()
    {
        return $this->hasMany(ReportStatus::class)->orderBy('created_at', 'asc');
    }

    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }

    public function latestReportStatus()
    {
        return $this->hasOne(ReportStatus::class)->latestOfMany();
    }

    public function latestStatus()
    {
        return $this->hasOne(ReportStatus::class)->latestOfMany();
    }

    /**
     * Get the priority level label based on calculated score
     */
    public function getPriorityLevelAttribute(): string
    {
        if (!$this->calculated_priority_score) {
            return 'Belum Dinilai';
        }
        
        $service = new ReportPriorityScoringService();
        return $service->getPriorityLevel($this->calculated_priority_score);
    }

    /**
     * Get the priority color for UI
     */
    public function getPriorityColorAttribute(): string
    {
        if (!$this->calculated_priority_score) {
            return 'gray';
        }
        
        $service = new ReportPriorityScoringService();
        return $service->getPriorityColor($this->calculated_priority_score);
    }

    /**
     * Get Bootstrap badge class for priority level
     */
    public function getPriorityBadgeClassAttribute(): string
    {
        if (!$this->calculated_priority_score) {
            return 'badge-secondary';
        }
        
        $service = new ReportPriorityScoringService();
        return $service->getPriorityBadgeClass($this->calculated_priority_score);
    }

    /**
     * Get effective priority score including aging factor
     */
    public function getEffectivePriorityScoreAttribute(): float
    {
        if (!$this->calculated_priority_score) {
            return 1.0;
        }
        
        $service = new ReportPriorityScoringService();
        return $service->calculateAgingPriority($this->created_at, $this->calculated_priority_score);
    }

    /**
     * Check if report has priority scoring
     */
    public function hasPriorityScoring(): bool
    {
        return !is_null($this->calculated_priority_score) && 
               $this->calculated_priority_score > 0;
    }

    /**
     * Get scoring criteria breakdown
     */
    public function getScoringBreakdownAttribute(): array
    {
        return [
            'category' => [
                'score' => $this->category_score,
                'weight' => ReportPriorityScoringService::WEIGHTS['category'],
                'weighted_score' => $this->category_score * ReportPriorityScoringService::WEIGHTS['category']
            ],
            'community_impact' => [
                'score' => $this->community_impact_score,
                'weight' => ReportPriorityScoringService::WEIGHTS['community_impact'],
                'weighted_score' => $this->community_impact_score * ReportPriorityScoringService::WEIGHTS['community_impact']
            ],
            'damage_level' => [
                'score' => $this->damage_level_score,
                'weight' => ReportPriorityScoringService::WEIGHTS['damage_level'],
                'weighted_score' => $this->damage_level_score * ReportPriorityScoringService::WEIGHTS['damage_level']
            ],
            'time_urgency' => [
                'score' => $this->time_urgency_score,
                'weight' => ReportPriorityScoringService::WEIGHTS['time_urgency'],
                'weighted_score' => $this->time_urgency_score * ReportPriorityScoringService::WEIGHTS['time_urgency']
            ]
        ];
    }

    /**
     * Scope for ordering by effective priority
     */
    public function scopeOrderByEffectivePriority($query)
    {
        $service = new ReportPriorityScoringService();
        return $service->addPriorityOrderByQuery($query);
    }

    /**
     * Scope for filtering by priority level (3-category system)
     */
    public function scopeByPriorityLevel($query, string $level)
    {
        switch ($level) {
            case 'tinggi':
                return $query->where('calculated_priority_score', '>=', 3.8);
            case 'sedang':
                return $query->where('calculated_priority_score', '>=', 2.6)
                            ->where('calculated_priority_score', '<', 3.8);
            case 'rendah':
                return $query->where('calculated_priority_score', '<', 2.6)
                            ->where('calculated_priority_score', '>', 0);
            default:
                return $query;
        }
    }

}
