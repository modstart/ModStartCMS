<?php


namespace Module\Cms\Util;


use ModStart\Core\Util\FileUtil;
use Module\Vendor\Provider\SiteTemplate\SiteTemplateProvider;

class CmsTemplateUtil
{
    static $roots = [
        'resources/views/theme/default/pc/cms/',
        'module/Cms/View/pc/cms/',
    ];

    private static function prepareTemplate()
    {
        $root = self::templateRoot() . 'pc/cms/';
        if ($root != self::$roots[0]) {
            array_unshift(self::$roots, $root);
        }
    }

    public static function allTemplateRoots()
    {
        $roots = [
            [
                'title' => 'CMS默认',
                'path' => 'module/Cms/View/pc/cms/',
            ]
        ];
        foreach (SiteTemplateProvider::all() as $provider) {
            $name = $provider->name();
            $root = "resources/views/theme/$name";
            if ($provider->root()) {
                $root = $provider->root();
                $root = str_replace(['::', '.'], '/', $root);
            }
            $root = rtrim($root, '/\\');
            $roots[] = [
                'title' => $provider->title(),
                'path' => $root,
            ];
        }
        return $roots;
    }

    public static function templateRoot()
    {
        $template = modstart_config()->getWithEnv('siteTemplate', 'default');
        $root = "resources/views/theme/$template";
        $provider = SiteTemplateProvider::get($template);
        if ($provider && $provider->root()) {
            $root = $provider->root();
            $root = str_replace(['::', '.'], '/', $root);
        }
        return rtrim($root, '/\\') . '/';
    }

    private static function listFiles($dir)
    {
        self::prepareTemplate();
        $files = [];
        foreach (self::$roots as $root) {
            $files = array_merge(
                $files,
                FileUtil::listFiles(base_path($root . $dir . '/'), '*.blade.php')
            );
        }
        $base = base_path();
        foreach ($files as $k => $v) {
            $files[$k]['_path'] = ltrim(substr($v['pathname'], strlen($base)), '/\\');
        }
        $results = [];
        foreach ($files as $file) {
            if (isset($results[$file['filename']])) {
                $results[$file['filename']][] = $file;
            } else {
                $results[$file['filename']] = [$file];
            }
        }
        return $results;
    }

    public static function allListTemplates()
    {
        return self::listFiles('list');
    }

    public static function allDetailTemplates()
    {
        return self::listFiles('detail');
    }

    public static function allPageTemplates()
    {
        return self::listFiles('page');
    }

    public static function allFormTemplates()
    {
        return self::listFiles('form');
    }

    public static function allListTemplateMap()
    {
        return array_build(self::allListTemplates(), function ($k, $v) {
            return [$k, $k];
        });
    }

    public static function allDetailTemplateMap()
    {
        return array_build(self::allDetailTemplates(), function ($k, $v) {
            return [$k, $k];
        });
    }

    public static function allPageTemplateMap()
    {
        return array_build(self::allPageTemplates(), function ($k, $v) {
            return [$k, $k];
        });
    }

    public static function allFormTemplateMap()
    {
        return array_build(self::allFormTemplates(), function ($k, $v) {
            return [$k, $k];
        });
    }

    public static function toBladeView($view)
    {
        if (empty($view)) {
            return $view;
        }
        if (ends_with($view, '.blade.php')) {
            return substr($view, 0, -10);
        }
        return $view;
    }
}
