<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit792820e79c8789b90e637cf8e234db91
{
    public static $prefixLengthsPsr4 = array (
        'B' => 
        array (
            'Bedivierre\\Craftsman\\Poetry\\' => 28,
            'Bedivierre\\Craftsman\\Masonry\\' => 29,
            'Bedivierre\\Craftsman\\Cartography\\' => 33,
            'Bedivierre\\Craftsman\\Aqueduct\\Flow\\' => 35,
            'Bedivierre\\Craftsman\\Aqueduct\\' => 30,
            'Bedivierre\\Craftsman\\Appraise\\' => 30,
            'Bedivierre\\Craftsman\\' => 21,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Bedivierre\\Craftsman\\Poetry\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/String',
        ),
        'Bedivierre\\Craftsman\\Masonry\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/DOM',
        ),
        'Bedivierre\\Craftsman\\Cartography\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/Routing',
        ),
        'Bedivierre\\Craftsman\\Aqueduct\\Flow\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/DataTransport/Transport',
        ),
        'Bedivierre\\Craftsman\\Aqueduct\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/DataTransport',
        ),
        'Bedivierre\\Craftsman\\Appraise\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/DataAssert',
        ),
        'Bedivierre\\Craftsman\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit792820e79c8789b90e637cf8e234db91::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit792820e79c8789b90e637cf8e234db91::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}