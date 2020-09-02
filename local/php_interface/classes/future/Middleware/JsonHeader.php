<?php

namespace Future\Middleware;

class JsonHeader
{
    public static function handle()
    {
        header('Content-Type: application/json');
    }
}
