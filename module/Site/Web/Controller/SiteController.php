<?php


namespace Module\Site\Web\Controller;


use ModStart\Core\Input\InputPackage;
use ModStart\Module\ModuleBaseController;

class SiteController extends ModuleBaseController
{
    public function contact()
    {
        $input = InputPackage::buildFromInput();
        $dialog = $input->getBoolean('dialog');
        $view = 'site.contact';
        if ($dialog) {
            $view = 'site.contactDialog';
            $this->shareDialogPageViewFrame();
        }
        return $this->view($view);
    }
}
