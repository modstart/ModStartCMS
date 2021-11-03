<?php


namespace Module\Cms\Util;


use ModStart\Core\Util\FileUtil;

class CmsTemplateUtil
{
    static $roots = [
        'resources/views/theme/default/pc/cms/',
        'module/Cms/View/pc/cms/',
    ];

    private static function listFiles($dir)
    {
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
