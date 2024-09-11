<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendanceModel extends Model
{
    use SoftDeletes;

    protected $table = 'attendance';

    protected $fillable = [
        'worker_id',
        'user_number',
        'entry_time',
        'exit_time',
        'date',
        'is_late',
    ];

    // Relación con el modelo Worker
    public function worker()
    {
        return $this->belongsTo(WorkerModel::class);
    }
}
