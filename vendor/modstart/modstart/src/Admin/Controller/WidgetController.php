<?php


namespace ModStart\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Widget\AbstractWidget;
use ModStart\Widget\Traits\HasRequestTrait;

class WidgetController extends Controller
{
    public static $PermitMethodMap = [
        'request' => '*',
    ];

    public function request()
    {
        $input = InputPackage::buildFromInput();
        $name = $input->getTrimString('_name');
        BizException::throwsIfEmpty('Invalid Param (name empty)', $name);
        BizException::throwsIf('Invalid Param (format error)', !preg_match('/^[a-zA-Z0-9\\\\]+$/', $name));
        BizException::throwsIf('Invalid Param (not exists)', !class_exists($name));
        BizException::throwsIf('Invalid Param (not Widget)', !is_subclass_of($name, AbstractWidget::class));
        /** @var HasRequestTrait $ins */
        $ins = app($name);
        BizException::throwsIf(L('No Permission'), !$ins->permit());
        return $ins->request();
    }
}
