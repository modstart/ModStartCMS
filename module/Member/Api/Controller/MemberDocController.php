<?php


namespace Module\Member\Api\Controller;


use Illuminate\Routing\Controller;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;


/**
 * @Api 用户账号
 */
class MemberDocController extends Controller
{
    /**
     * @Api 用户协议
     * @ApiBodyParam type string 协议类型 agreement用户使用协议, privacy用户隐私协议, appeal账号申诉
     * @ApiResponseData
     * {
     *  "title": "协议标题",
     *  "content": "<p>协议内容富文本</p>"
     * }
     */
    public function get()
    {
        $input = InputPackage::buildFromInput();
        $type = $input->getTrimString('type');
        BizException::throwsIf('类型错误', !in_array($type, ['agreement', 'privacy', 'appeal']));
        return Response::generateSuccessData([
            'title' => modstart_config('Member_' . ucfirst($type) . 'Title'),
            'content' => modstart_config('Member_' . ucfirst($type) . 'Content'),
        ]);
    }
}
