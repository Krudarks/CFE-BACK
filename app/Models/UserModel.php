<?php

namespace App\Models;

use App\Constants\Queue\QueueConstants;
use App\Jobs\NewUserJob;
use App\Models\Passport\OauthAccessToken;
use App\Notifications\PasswordResetNotification as PasswordReset;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class UserModel extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * Send the password reset notification.
     * @param string $token
     * @return void
     */
    public function sendPasswordResetNotification($token): void
    {
        if (request("newUser")) {
            NewUserJob::dispatch($token, request('email'))->onQueue(QueueConstants::EMAIL_NOTIFICATION);
        } else {
            $this->notify(new PasswordReset($token, $this->getEmailForPasswordReset()));
        }
    }

    /**
     * @return HasMany
     */
    public function AauthAcessToken(): HasMany
    {
        return $this->hasMany(OauthAccessToken::class);
    }

    public function role(): HasOne
    {
        return $this->hasOne(RoleModel::class, 'id', 'role_id');
    }

    public function worker(): HasOne
    {
        return $this->hasOne(WorkerModel::class, 'user_id', 'id');
    }

    public function test(): HasOne
    {
        return $this->hasOne(TestModel::class, 'user_id', 'id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(NotesModel::class, 'user_id', 'id');
    }

}
