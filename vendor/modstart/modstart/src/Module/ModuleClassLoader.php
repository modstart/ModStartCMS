<?php


namespace ModStart\Module;


use Composer\Autoload\ClassLoader;

/**
 * 模块命名空间动态加载器
 *
 * Class ModuleClassLoader
 * @package ModStart\Module
 */
class ModuleClassLoader
{
    /** @var ClassLoader $loader */
    private static $loader = null;
    private static $namespacesAdded = [];

    private static function loaderInit()
    {
        if (null == self::$loader) {
            self::$loader = app(ClassLoader::class);
            self::$loader->register(true);
        }
    }

    public static function addClass($class, $file)
    {
        self::loaderInit();
        self::$loader->addClassMap([$class => $file]);
    }

    public static function addNamespace($namespace, $path)
    {
        self::loaderInit();
        if (!ends_with($namespace, '\\')) {
            $namespace = $namespace . '\\';
        }
        $namespacesAdded[$namespace] = $path;
        self::$loader->addPsr4($namespace, [$path]);
    }

    public static function addNamespaceIfMissing($namespace, $path)
    {
        if (!self::hasNamespace($namespace)) {
            self::addNamespace($namespace, $path);
        }
    }

    /**
     * @param $namespace
     * @return bool
     * @since 1.6.0
     */
    public static function hasNamespace($namespace)
    {
        if (!ends_with($namespace, '\\')) {
            $namespace = $namespace . '\\';
        }
        return isset($namespacesAdded[$namespace]);
    }
}
