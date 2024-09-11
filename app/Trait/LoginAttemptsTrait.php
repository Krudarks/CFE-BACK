<?php

namespace App\Trait;

use Exception;
use Illuminate\Support\Facades\Cache;

trait LoginAttemptsTrait
{

    protected int $blockedTime = 15;

    protected int $allowedLoginAttempts = 3;

    /**
     * @param $email
     * @throws Exception
     */
    function checkLoginAttempt($email): void
    {
        if ($this->isUserBlocked($email)) {
            $this->blockUserTemporarily($email);
        }

        $attemptNumber = Cache::get("login_attempt_$email");

        if (is_null($attemptNumber)) {
            Cache::put("login_attempt_$email", 1, $this->blockedTime);
            return;
        }

        if ($attemptNumber >= $this->allowedLoginAttempts)
            $this->blockUserTemporarily($email);
        else
            Cache::put("login_attempt_$email", $attemptNumber + 1, $this->blockedTime);
    }

    /**
     * @param $email
     * @throws Exception
     */
    function blockUserTemporarily($email): void
    {
        if (!Cache::put("login_temporarily_blocked_$email", $email, $this->blockedTime))
            throw new Exception("ERROR_REDIS_STORE_ATTEMPT_TEMPORARILY_BLOCK_USER");

        throw new Exception("Usuario bloqueado temporalmente por intentos fallidos de inicio de sesión", 300);
    }

    /**
     * @param $email
     * @return mixed
     * @throws Exception
     */
    function isUserBlocked($email): mixed
    {
        try {
            return Cache::get("login_temporarily_blocked_$email");
        } catch (Exception $e) {
            throw new Exception("REDIS_EXCEPTION " . $e->getMessage());
        }
    }

    /**
     * @param $email
     */
    function clearLoginAttempts($email): void
    {
        Cache::forget("login_attempt_$email");
    }
}
