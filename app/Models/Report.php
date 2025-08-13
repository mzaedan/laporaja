<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        'urgency_level'
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
        return $this->hasMany(ReportStatus::class);
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

}
