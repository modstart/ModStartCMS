<?php


namespace ModStart\Admin\Concern;


use ModStart\Admin\Auth\AdminPermission;
use ModStart\Admin\Layout\AdminDialogPage;
use ModStart\Core\Input\Request;
use ModStart\Core\Util\CRUDUtil;
use ModStart\Form\Form;

trait HasAdminForm
{
    private function computeTitleForm($subject, $langId)
    {
        $title = ($subject ? $subject . ' ' . L($langId) : L($langId));
        return isset($this->pageTitle) ? $this->pageTitle : $title;
    }

    /**
     * @return Form
     */
    private function getForm()
    {
        /** @var Form $form */
        $form = $this->form();
        $form->showSubmit(false)->showReset(false);
        return $form;
    }

    public function add(AdminDialogPage $page)
    {
        $form = $this->getForm();
        if (Request::isPost()) {
            AdminPermission::demoCheck();
            return $form->addRequest();
        }
        return $page->pageTitle($this->computeTitleForm($form->title(), 'Add'))->body($form->add());
    }

    public function edit(AdminDialogPage $page)
    {
        $form = $this->getForm();
        if (Request::isPost()) {
            AdminPermission::demoCheck();
            return $form->editRequest(CRUDUtil::id());
        }
        return $page->pageTitle($this->computeTitleForm($form->title(), 'Edit'))->body($form->edit(CRUDUtil::id()));
    }

    public function delete()
    {
        AdminPermission::demoCheck();
        return $this->getForm()->deleteRequest(CRUDUtil::ids());
    }

    public function sort()
    {
        AdminPermission::demoCheck();
        return $this->getForm()->sortRequest(CRUDUtil::id());
    }
}
