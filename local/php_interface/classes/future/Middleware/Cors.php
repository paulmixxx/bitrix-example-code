<?php

namespace Future\Middleware;

class Cors
{
    public static function handle()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: *");
    }
}
