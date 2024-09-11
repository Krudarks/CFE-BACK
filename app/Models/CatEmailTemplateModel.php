<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;

class CatEmailTemplateModel extends Model
{
    protected $table = "cat_email_template";

    protected $fillable = [
        "template",
        "original_template",
        "title",
        "code",
        "description",
    ];

    protected $hidden = [
    ];

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
