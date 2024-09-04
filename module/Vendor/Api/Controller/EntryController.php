<?php

namespace Module\Vendor\Api\Controller;

use Illuminate\Routing\Controller;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use Module\Vendor\Event\EntryBizEvent;

class EntryController extends Controller
{
    public function biz()
    {
        $input = InputPackage::buildFromInput();
        $name = $input->getTrimString('name');
        $param = $input->getArray('param');
        EntryBizEvent::fire($name, $param);
        return Response::generateSuccess();
    }
}
