<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit158724aa935d79d4bfc2f5ece691b0b6
{
    public static $files = array (
        '867abbb971c5fd66c2d95fdd6206c6ba' => __DIR__ . '/..' . '/bifei/swostar/src/Supper/Helper.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'SwoStar\\' => 8,
        ),
        'D' => 
        array (
            'Database\\' => 9,
        ),
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'SwoStar\\' => 
        array (
            0 => __DIR__ . '/..' . '/bifei/swostar/src',
        ),
        'Database\\' => 
        array (
            0 => __DIR__ . '/../..' . '/database',
        ),
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit158724aa935d79d4bfc2f5ece691b0b6::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit158724aa935d79d4bfc2f5ece691b0b6::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
