<?php

namespace Future\Middleware;

use Exception;

class Auth
{
    const USER_ID = 1;

    /**
     * @throws Exception
     */
    public static function handle()
    {
        global $USER;

        if (!$token = env('TOKEN_API')) {
            throw new Exception("Token api not init", 500);
        }

        if ($_REQUEST["token"] == $token) {
            $USER->Authorize(self::USER_ID);
        }

        if (!$USER->IsAuthorized()) {
            throw new Exception("Unauthorized", 401);
        }
    }
}
