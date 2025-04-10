<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportCategory extends Model
{
    protected $fillable = [
        'name',
        'image'
    ];

    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}
