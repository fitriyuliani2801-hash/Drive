<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CronLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'command_name',
        'status',
        'comments_fetched_count',
        'duration_seconds',
        'log_message',
        'executed_at',
    ];

    protected $casts = [
        'executed_at' => 'datetime',
        'duration_seconds' => 'float',
    ];
}
