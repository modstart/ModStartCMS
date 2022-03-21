<?php


namespace Module\Member\Api\Controller;


use Illuminate\Routing\Controller;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;

class MemberDocController extends Controller
{
    public function get()
    {
        $input = InputPackage::buildFromInput();
        $type = $input->getTrimString('type');
        BizException::throwsIf('类型错误', !in_array($type, ['agreement', 'privacy']));
        return Response::generateSuccessData([
            'title' => modstart_config('Member_' . ucfirst($type) . 'Title'),
            'content' => modstart_config('Member_' . ucfirst($type) . 'Content'),
        ]);
    }
}
