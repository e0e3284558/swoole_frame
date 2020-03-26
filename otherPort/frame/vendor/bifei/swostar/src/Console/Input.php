<?php

namespace SwoStar\Console;
class Input
{
    public static function info($message, $description = null)
    {
        $return = "=====>" . $description . 'start \n ';
        if (is_array($message)) {
            $return = $return . var_export($message, true);
        } else {
            $return .= $message . "\n";
        }
        $return .= '=====>' . $description . " end \n";
        echo $return;

    }
}