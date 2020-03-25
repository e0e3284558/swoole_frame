<?php

namespace SwoCloud\Supper;

/**
 * 用于负载
 * Class Arithmetic
 * @package SwoStar\Supper
 */
class Arithmetic
{
    protected static $roundLastIndex = 0;

    public static function round(array $list)
    {
        $index = self::$roundLastIndex;
        $url = $list[$index];

        if ($index + 1 > count($list) - 1) {
            self::$roundLastIndex = 0;
        } else {
            self::$roundLastIndex++;
        }
        return $url;
    }

    public static function hash()
    {
        
    }
}