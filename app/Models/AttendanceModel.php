<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendanceModel extends Model
{

    use SoftDeletes;

    protected $table = 'attendance';

    protected $fillable = [
        'worker_id',
        'entry_time',
        'exit_time',
        'date',
        'worker_count',
        'user_number',
    ];

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    // Relación con el modelo Worker
    public function worker(): BelongsTo
    {
        return $this->belongsTo(WorkerModel::class);
    }
}
