<?php


namespace ModStart\Admin\Concern;


use ModStart\Admin\Layout\AdminPage;
use ModStart\Core\Input\Request;
use ModStart\Core\Util\CRUDUtil;
use ModStart\Grid\Grid;

trait HasAdminGrid
{
    public $registerGridClass = null;

    private function computeTitleGrid($subject, $langId)
    {
        $title = ($subject ? $subject . ' ' . L($langId) : L($langId));
        return isset($this->pageTitle) ? $this->pageTitle : $title;
    }

    public function index(AdminPage $page)
    {
        /** @var Grid $grid */
        $grid = $this->grid();
        CRUDUtil::registerGridResource($grid, $this->registerGridClass ? $this->registerGridClass : '\\' . __CLASS__);
        if (Request::isPost()) {
            return $grid->request();
        }
        return $page->pageTitle($this->computeTitleGrid($grid->title(), 'List'))->body($grid);
    }
}
