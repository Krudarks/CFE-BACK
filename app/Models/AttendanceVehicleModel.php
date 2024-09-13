<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceVehicleModel extends Model
{
    protected $table = 'vehicle_usage';

    protected $fillable = [
        'vehicle_id',
        'status_id',
        'date',
    ];

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
