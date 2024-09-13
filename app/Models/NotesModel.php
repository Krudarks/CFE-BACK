<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotesModel extends Model
{

    protected $table = 'notes';

    protected $fillable = [
        'user_id',
        'description',
    ];

    public function user(): HasOne
    {
        return $this->hasOne(UserModel::class, 'id', 'user_id');
    }

}
