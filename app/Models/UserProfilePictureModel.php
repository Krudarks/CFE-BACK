<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserProfilePictureModel extends Model
{

    use SoftDeletes;

    protected $table = "user_profile_picture";

    protected $fillable = [
        "user_id",
        "crop_setting",
        "path",
        "path_original",
        "disk",
    ];

    protected $hidden = [];

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

}
