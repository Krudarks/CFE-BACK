<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StatusCarModel extends Model
{
//    use SoftDeletes;

    protected $table = 'status_car';

    protected $fillable = [
        'name',
        'code',
        'description',
    ];

}
