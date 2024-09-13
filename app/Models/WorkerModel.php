<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkerModel extends Model
{
    use SoftDeletes;

    protected $table = 'workers';

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'phone',
        'user_number',
    ];

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class);
    }
}
