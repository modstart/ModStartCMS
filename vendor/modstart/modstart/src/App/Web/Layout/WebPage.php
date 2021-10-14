<?php


namespace ModStart\App\Web\Layout;

use ModStart\Core\Util\AgentUtil;
use ModStart\Layout\Page;

class WebPage extends Page
{
    public function view($view = null)
    {
        if (null === $view) {
            if ($this->view) {
                return $this->view;
            }
            $template = modstart_config()->getWithEnv('siteTemplate','default');

            $mobileView = "theme.$template.m.page";
            $mobileViewDefault = "theme.default.m.page";

            $pcView = "theme.$template.pc.page";
            $pcViewDefault = "theme.default.pc.page";

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
            return 'modstart::app.web.page';
        }
        return parent::view($view);
    }

    public function useBlank()
    {
        $this->view = 'modstart::app.web.pageBlank';
    }

    public function useNarrow()
    {
        $this->view = 'modstart::app.web.pageNarrow';
    }
}
