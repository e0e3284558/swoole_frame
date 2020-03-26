<?php

use  SwoStar\Foundation\Application;

if (!function_exists('app')) {
    function app($a = null)
    {
        if (empty($a)) {
            return Application::getInstance();
        }
        return Application::getInstance()->make($a);
    }
}

if (!function_exists('dd')) {
    function dd($message, $description = null)
    {
        \SwoStar\Console\Input::info($message, $description);
    }
}