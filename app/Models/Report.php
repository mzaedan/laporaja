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
        'address'
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

    public function feedback()
    {
        return $this->hasOne(Feedback::class);
    }

    public function latestReportStatus()
    {
        return $this->hasOne(ReportStatus::class)->latestOfMany();
    }

}
