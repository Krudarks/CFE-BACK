<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleModel extends Model
{
    protected $table = 'vehicles';

    protected $fillable = [
        "brand",
        "model",
        "plates",
        "vehicle_number",
    ];

}
