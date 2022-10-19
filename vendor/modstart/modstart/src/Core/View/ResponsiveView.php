<?php


namespace ModStart\Core\View;


use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Module\Vendor\Provider\SiteTemplate\SiteTemplateProvider;

class ResponsiveView
{
    public static function templateRoot()
    {
        static $provider = null;
        static $templateRoot = null;
        if (null !== $templateRoot) {
            return $templateRoot;
        }
        static $templateName = 'default';
        $msSiteTemplate = Input::get('msSiteTemplate', null);
        if (!empty($msSiteTemplate)) {
            $provider = SiteTemplateProvider::get($msSiteTemplate);
            if (!empty($provider)) {
                Session::put('msSiteTemplate', $msSiteTemplate);
            }
        }
        if (empty($provider)) {
            $msSiteTemplate = Session::get('msSiteTemplate', null);
            if (!empty($msSiteTemplate)) {
                $provider = SiteTemplateProvider::get($msSiteTemplate);
                if (empty($provider)) {
                    Session::forget('msSiteTemplate');
                }
            }
        }
        if (empty($provider)) {
            $templateName = modstart_config()->getWithEnv('siteTemplate', 'default');
            $provider = SiteTemplateProvider::get($templateName);
        }
        if ($provider && $provider->root()) {
            $templateRoot = $provider->root();
        } else {
            $templateRoot = "theme.$templateName";
        }
        return $templateRoot;
    }

    public static function templateRootRealpath($module)
    {
        $root = self::templateRoot();
        if (Str::startsWith($root, 'module::')) {
            $root = str_replace(['::', '.'], '/', $root);
            $root = base_path($root);
        } else if (Str::startsWith($root, 'theme.')) {
            $root = 'resources/views/' . str_replace(['.'], '/', $root);
            $root = base_path($root);
            if (!file_exists($root)) {
                $root = base_path('module/' . $module . '/View');
            }
            if (!file_exists($root)) {
                $root = base_path('resources/views/theme/default');
            }
        } else {
            $root = base_path($root);
        }
        return rtrim($root, '\\/') . '/';
    }
}
