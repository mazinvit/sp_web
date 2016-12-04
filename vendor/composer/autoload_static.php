<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit4923875843e6b35dedebb86426cae555
{
    public static $files = array (
        '92c8763cd6170fce6fcfe7e26b4e8c10' => __DIR__ . '/..' . '/symfony/phpunit-bridge/bootstrap.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Symfony\\Component\\Debug\\' => 24,
            'Symfony\\Bridge\\PhpUnit\\' => 23,
        ),
        'P' => 
        array (
            'Psr\\Log\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Symfony\\Component\\Debug\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/debug',
        ),
        'Symfony\\Bridge\\PhpUnit\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/phpunit-bridge',
        ),
        'Psr\\Log\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/log/Psr/Log',
        ),
    );

    public static $prefixesPsr0 = array (
        'T' => 
        array (
            'Twig_' => 
            array (
                0 => __DIR__ . '/../..' . '/lib',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit4923875843e6b35dedebb86426cae555::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit4923875843e6b35dedebb86426cae555::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit4923875843e6b35dedebb86426cae555::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}