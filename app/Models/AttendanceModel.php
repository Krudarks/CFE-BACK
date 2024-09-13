<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Psr\Log\LoggerInterface;

class AttendanceModel extends Model
{
    protected LoggerInterface $log;

    use SoftDeletes;

    protected $table = 'attendance';

    protected $fillable = [
        'worker_id',
        'entry_time',
        'exit_time',
        'date',
        'worker_count'
    ];

    // Relación con el modelo Worker
    public function worker()
    {
        return $this->belongsTo(WorkerModel::class);
    }
}
