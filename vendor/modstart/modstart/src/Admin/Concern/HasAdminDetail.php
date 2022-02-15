<?php


namespace ModStart\Admin\Concern;


use ModStart\Admin\Layout\AdminDialogPage;
use ModStart\Core\Util\CRUDUtil;

trait HasAdminDetail
{
    private function computeTitleDetail($subject, $langId)
    {
        $title = ($subject ? $subject . ' ' . L($langId) : L($langId));
        return isset($this->pageTitle) ? $this->pageTitle : $title;
    }

    public function show(AdminDialogPage $page)
    {
        $detail = $this->detail();
        return $page->pageTitle($this->computeTitleDetail($detail->title(), 'Show'))->body($detail->show(CRUDUtil::id()));
    }
}
