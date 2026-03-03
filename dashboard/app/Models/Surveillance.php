<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Surveillance extends Model
{
    protected $table = 'surveillances';
    public $timestamps = false;

    protected $fillable = [
        'code',
        'name_en',
        'name_kh',
        'start_date',
        'end_date'
    ];

    public function cases()
    {
        return $this->hasMany(SurveillanceCase::class, 'surveillance_id');
    }
}