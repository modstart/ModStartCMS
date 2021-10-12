<?php


namespace ModStart\Core\View;

use Illuminate\Support\Facades\View;
use ModStart\Core\Util\AgentUtil;

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
        $template = modstart_config('siteTemplate', 'default');
        $module = $this->getModule();

        $mobileView = "theme.$template.m.$view";
        $mobileViewDefault = "theme.default.m.$view";
        if ($module) {
            $mobileViewModule = "module::$module.View.m.$view";
        }

        $pcView = "theme.$template.pc.$view";
        $pcViewDefault = "theme.default.pc.$view";
        if ($module) {
            $pcViewModule = "module::$module.View.pc.$view";
        }

        $mobileFrameView = "theme.$template.m.frame";
        $mobileFrameViewDefault = "theme.default.m.frame";

        $pcFrameView = "theme.$template.pc.frame";
        $pcFrameViewDefault = "theme.default.pc.frame";

        $useView = $pcView;
        $useFrameView = $pcFrameView;
        if ($this->isMobile()) {
            $useView = $mobileView;
            if (!view()->exists($useView)) {
                $useView = $mobileViewDefault;
                if ($module) {
                    if (!view()->exists($useView)) {
                        $useView = $mobileViewModule;
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
