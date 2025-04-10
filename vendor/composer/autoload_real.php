<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInit5b37e3b82fd5c3b4c1a76ef8e09d7b6c
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(array('ComposerAutoloaderInit5b37e3b82fd5c3b4c1a76ef8e09d7b6c', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInit5b37e3b82fd5c3b4c1a76ef8e09d7b6c', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInit5b37e3b82fd5c3b4c1a76ef8e09d7b6c::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
