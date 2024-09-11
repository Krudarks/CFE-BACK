<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestModel extends Model
{
    use SoftDeletes;

    protected $table = 'tests';

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'phone',
    ];

    public function user(): HasOne
    {
        return $this->hasOne(UserModel::class, 'id', 'user_id');
    }
}
