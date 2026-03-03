<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaseLabResult extends Model
{
    protected $table = 'case_lab_results';
    public $timestamps = false;

    protected $fillable = [
        'lab_code',
        'is_positive',
        'pathogen_name',
        'subtype',
        'indicator'
    ];
}