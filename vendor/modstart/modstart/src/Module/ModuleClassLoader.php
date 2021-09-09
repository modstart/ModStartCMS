<?php


namespace ModStart\Module;


use Composer\Autoload\ClassLoader;

class ModuleClassLoader
{
    
    private static $loader = null;

    public static function addNamespace($namespace, $path)
    {
        if (null == self::$loader) {
            self::$loader = app(ClassLoader::class);
            self::$loader->register(true);
        }
        if (!ends_with($namespace, '\\')) {
            $namespace = $namespace . '\\';
        }
        self::$loader->addPsr4($namespace, [$path]);
    }
}
