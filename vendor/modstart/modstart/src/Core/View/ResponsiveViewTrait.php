<?php


namespace ModStart\Core\View;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Util\AgentUtil;
use Module\Vendor\Provider\SiteTemplate\SiteTemplateProvider;

trait ResponsiveViewTrait
{
    protected $_viewBase = null;

    public function getModule()
    {
        static $module = null;
        if (null === $module) {
            $cls = get_class($this);
            if (preg_match('/^Module\\\\([\\w]+).*?/', $cls, $mat)) {
                $module = $mat[1];
            } else if (method_exists($this, 'getCurrentModule')) {
                $module = $this->getCurrentModule();
            } else {
                $module = '';
            }
        }
        return $module;
    }

    private function fetchViewPath($templateName, $templateRoot, $module, $device, $view)
    {
        if (Str::contains($view, '::')) {
            return $view;
        }
        $viewThemeCustom = "theme.$templateName.$device.$view";
        $viewTheme = "$templateRoot.$device.$view";
        if ($module) {
            $viewModule = "module::$module.View.$device.$view";
        }
        $viewDefault = "theme.default.$device.$view";
        if (view()->exists($viewThemeCustom)) {
            return $viewThemeCustom;
        }
        if (view()->exists($viewTheme)) {
            return $viewTheme;
        }
        if ($module) {
            if (view()->exists($viewModule)) {
                return $viewModule;
            }
        }
        if (view()->exists($viewDefault)) {
            return $viewDefault;
        }
        return null;
    }

    /**
     * @param $view
     * @return array
     * @throws BizException
     *
     * @example
     * list($view, $viewFrame) = $this->viewPaths('member.index')
     */
    protected function viewPaths($view)
    {
        static $templateName = null;
        static $provider = null;
        static $module = null;
        static $templateRoot = null;
        if (null === $templateName) {
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
            $module = $this->getModule();
            if (empty($provider)) {
                $templateName = modstart_config()->getWithEnv('siteTemplate', 'default');
                $provider = SiteTemplateProvider::get($templateName);
            }
            if ($provider && $provider->root()) {
                $templateRoot = $provider->root();
                $templateName = $provider->name();
            } else {
                $templateRoot = "theme.$templateName";
            }
            Session::put('msSiteTemplateUsing', $templateName);
        }

        $useView = null;
        $useFrameView = null;
        if ($this->isMobile()) {
            $useView = $this->fetchViewPath($templateName, $templateRoot, $module, 'm', $view);
            $useFrameView = $this->fetchViewPath($templateName, $templateRoot, $module, 'm', 'frame');
        }
        if (empty($useView)) {
            $useView = $this->fetchViewPath($templateName, $templateRoot, $module, 'pc', $view);
        }
        if (empty($useFrameView)) {
            $useFrameView = $this->fetchViewPath($templateName, $templateRoot, $module, 'pc', 'frame');
        }
        // print_r([$view, $useView, $useFrameView]); exit();
        View::share('_viewFrame', $useFrameView);
        BizException::throwsIfEmpty('View Not Exists : ' . $view, $useView);
        return [$useView, $useFrameView];
    }

    protected function shareDialogPageViewFrame()
    {
        list($_viewFrameDialog, $_) = $this->viewPaths('dialogPage');
        View::share('_viewFrameDialog', $_viewFrameDialog);
    }

    protected function view($view, $viewData = [])
    {
        list($view, $frameView) = $this->viewPaths($view);
        // return [$view, $frameView];
        return view($view, $viewData);
    }

    protected function viewRender($view, $viewData = [])
    {
        list($view, $frameView) = $this->viewPaths($view);
        return view($view, $viewData)->render();
    }

    protected function viewRealpath($view)
    {
        return View::getFinder()->find($view);
    }

    protected function isMobile()
    {
        return AgentUtil::isMobile();
    }

    protected function isPC()
    {
        return AgentUtil::isPC();
    }


}
