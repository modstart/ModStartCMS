<?php


namespace ModStart\Admin\Concern;


use ModStart\Admin\Layout\AdminDialogPage;
use ModStart\Admin\Layout\AdminPage;
use ModStart\Core\Input\Request;
use ModStart\Core\Util\CRUDUtil;
use ModStart\Grid\Grid;

trait HasAdminGrid
{
    public $registerGridClass = null;
    public $gridPageLayout = 'default';
    public $gridPageUrlParam = [];

    private function computeTitleGrid($subject, $langId)
    {
        $title = ($subject ? $subject . ' ' . L($langId) : L($langId));
        return isset($this->pageTitle) ? $this->pageTitle : $title;
    }

    public function useGridDialogPage()
    {
        $this->gridPageLayout = 'dialog';
    }

    public function setGridPageUrlParam($param)
    {
        $this->gridPageUrlParam = $param;
    }

    public function index()
    {
        switch ($this->gridPageLayout) {
            case 'dialog':
                $page = app(AdminDialogPage::class);
                break;
            default:
                $page = app(AdminPage::class);
                break;
        }
        /** @var Grid $grid */
        $grid = $this->grid();
        CRUDUtil::registerGridResource($grid, $this->registerGridClass ? $this->registerGridClass : '\\' . __CLASS__, $this->gridPageUrlParam);
        if (Request::isPost()) {
            return $grid->request();
        }
        return $page->pageTitle($this->computeTitleGrid($grid->title(), 'List'))->body($grid);
    }
}
