<?php

namespace Module\AigcBase\Admin\Controller;

use Illuminate\Routing\Controller;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\LogUtil;
use ModStart\Core\Util\SessionUtil;
use Module\Aicc\Model\BizUser;
use Module\AigcBase\Provider\AigcChatProvider;

class AigcChatController extends Controller
{
    public static $PermitMethodMap = [
        '*' => '*',
    ];

    public function index($type)
    {
        Response::textEventStreamed(function ($sendCallback, $param = []) use ($type) {
            $input = InputPackage::buildFromInput();
            $prompt = $input->getTrimString('prompt');
            $driver = null;
            switch ($type) {
                case 'ueditor':
                    $driver = modstart_config('AigcBase_AdminRichEditorDriver');
                    break;
            }
            try {
                if (!$driver) {
                    BizException::throws('机器人没有配置，请在 后台→系统设置→AI平台对接→功能设置 中配置');
                }
                $provider = AigcChatProvider::getByFullName($driver);
                if (empty($provider)) {
                    BizException::throws('机器人没有配置');
                } else {
                    $option = [];
                    $ret = $provider->chatStream(function ($payload, $param) use (&$sendCallback, &$send) {
                        call_user_func($sendCallback, $payload['type'], isset($payload['data']) ? $payload['data'] : null);
                    }, 'Admin_' . SessionUtil::id(), [
                        'type' => 'text',
                        'content' => $prompt,
                    ], $option);
                    if (Response::isError($ret)) {
                        BizException::throws($ret['msg']);
                    }
                }
                LogUtil::info('xxxx', $ret);
            } catch (BizException $e) {
                call_user_func($sendCallback, 'error', $e->getMessage());
            }
        });
    }
}
