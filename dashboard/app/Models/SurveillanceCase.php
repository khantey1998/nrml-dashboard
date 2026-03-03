<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveillanceCase extends Model
{
    protected $table = 'surveillance_cases';
    public $timestamps = false;

    protected $fillable = [
        'lab_code',
        'case_date',
        'is_newcase',
        'sentinel_site_name',
        'site_province_name',
        'surveillance_id',
        'year_data',
        'week_data',
        'patient_age_inday',
        'patient_sex',
        'is_alive',
        'patient_privince'
    ];

    public function surveillance()
    {
        return $this->belongsTo(Surveillance::class, 'surveillance_id');
    }
}