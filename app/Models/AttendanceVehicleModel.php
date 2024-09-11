<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceVehicleModel extends Model
{
    protected $table = 'vehicle_usage';

    protected $fillable = [
        'worker_id',
        'vehicle_id',
        'status_id',
        'date',
        'start_time',
        'end_time',
    ];

    // Relación con el modelo Worker
    public function worker()
    {
        return $this->belongsTo(WorkerModel::class);
    }

    // Relación con el modelo Vehicle
    public function vehicle()
    {
        return $this->belongsTo(VehicleModel::class);
    }

    public function status()
    {
        return $this->belongsTo(StatusCarModel::class);
    }
}
