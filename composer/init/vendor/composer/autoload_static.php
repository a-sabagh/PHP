<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInite89e90f2f0db5875f33f1abf439b22b7
{
    public static $files = array (
        'f6cbce736bc5fc2e5188a8b035469160' => __DIR__ . '/..' . '/josh/faker/src/helpers.php',
    );

    public static $prefixLengthsPsr4 = array (
        'J' => 
        array (
            'Josh\\Faker\\' => 11,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Josh\\Faker\\' => 
        array (
            0 => __DIR__ . '/..' . '/josh/faker/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInite89e90f2f0db5875f33f1abf439b22b7::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInite89e90f2f0db5875f33f1abf439b22b7::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
