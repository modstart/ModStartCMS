<?php


namespace ModStart\App\Web\Layout;

use ModStart\Core\Util\AgentUtil;
use ModStart\Layout\Page;

class WebDialogPage extends Page
{
    public function view($view = null)
    {
        if (null === $view) {

            if ($this->view) {
                return $this->view;
            }

            $template = modstart_config()->getWithEnv('siteTemplate','default');

            $mobileView = "theme.$template.m.dialogPage";
            $mobileViewDefault = "theme.default.m.dialogPage";

            $pcView = "theme.$template.pc.dialogPage";
            $pcViewDefault = "theme.default.pc.dialogPage";

            if (AgentUtil::isMobile()) {
                if (view()->exists($mobileView)) {
                    return $mobileView;
                }
                if (view()->exists($mobileViewDefault)) {
                    return $mobileViewDefault;
                }
            }
            if (view()->exists($pcView)) {
                return $pcView;
            }
            if (view()->exists($pcViewDefault)) {
                return $pcViewDefault;
            }

            return 'modstart::app.web.dialogPage';
        }
        return parent::view($view);
    }
}
