<?php


namespace ModStart\Core\View;

use Illuminate\Support\Facades\View;
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
            } else {
                $module = '';
            }
        }
        return $module;
    }

    protected function viewPaths($view)
    {
        static $template = null;
        static $provider = null;
        static $module = null;
        static $templateRoot = null;
        if (null === $template) {
            $template = modstart_config()->getWithEnv('siteTemplate', 'default');
            $module = $this->getModule();
            $provider = SiteTemplateProvider::get($template);
            if ($provider && $provider->root()) {
                $templateRoot = $provider->root();
            } else {
                $templateRoot = "theme.$template";
            }

        }

        /**
         * 模板的View
         */
        $mobileView = "$templateRoot.m.$view";
        /**
         * 模板不存在时替代View
         */
        $mobileViewDefault = "theme.default.m.$view";
        /**
         * 是模块时，模块替代View
         */
        if ($module) {
            $mobileViewModule = "module::$module.View.m.$view";
        }

        $pcView = "$templateRoot.pc.$view";
        $pcViewDefault = "theme.default.pc.$view";
        if ($module) {
            $pcViewModule = "module::$module.View.pc.$view";
        }

        $mobileFrameView = "$templateRoot.m.frame";
        $mobileFrameViewDefault = "theme.default.m.frame";

        $pcFrameView = "$templateRoot.pc.frame";
        $pcFrameViewDefault = "theme.default.pc.frame";

        $useView = $pcView;
        $useFrameView = $pcFrameView;
        if ($this->isMobile()) {
            $useView = $mobileView;
            if (!view()->exists($useView)) {
                $useView = $pcView;
                if (!view()->exists($useView)) {
                    $useView = $mobileViewDefault;
                    if ($module) {
                        if (!view()->exists($useView)) {
                            $useView = $mobileViewModule;
                        }
                    }
                }
            }
            $useFrameView = $mobileFrameView;
            if (!view()->exists($useFrameView)) {
                $useFrameView = $mobileFrameViewDefault;
            }
        }
        if (!view()->exists($useView)) {
            $useView = $pcViewDefault;
            if ($module) {
                if (!view()->exists($useView)) {
                    $useView = $pcViewModule;
                }
            }
        }
        if (!view()->exists($useFrameView)) {
            $useFrameView = $pcFrameViewDefault;
        }
        // print_r([$view, $useView, $useFrameView]); exit();
        View::share('_viewFrame', $useFrameView);
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
