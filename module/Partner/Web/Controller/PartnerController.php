<?php


namespace Module\Partner\Web\Controller;

use ModStart\Module\ModuleBaseController;
use Module\Partner\Util\PartnerUtil;

class PartnerController extends ModuleBaseController
{

    public function index()
    {
        return $this->view('partner.index', [
            'records' => PartnerUtil::listByPosition('page'),
        ]);
    }

}
