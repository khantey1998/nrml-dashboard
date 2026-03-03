<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LastSyncedLog extends Model
{
    protected $table = 'last_synced_logs';
    public $timestamps = false;

    protected $fillable = [
        'surveillance_id',
        'last_synced_date'
    ];
}