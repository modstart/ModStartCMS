<?php


namespace Module\CaptchaTecmz\Api\Controller;


use Illuminate\Routing\Controller;
use ModStart\Core\Input\Response;

/**
 * Class IndexController
 * @package Module\CaptchaTecmz\Api\Controller
 * @Api 验证码
 */
class IndexController extends Controller
{
    /**
     * @Api 获取验证码配置
     * @ApiDesc 获取验证码服务AppId配置
     * @ApiMethod post
     * @ApiResponseData {
     *   "appId": "AppId"
     * }
     */
    public function info()
    {
        $config = modstart_config();
        return Response::generateSuccessData([
            'appId' => $config->getWithEnv('CaptchaTecmz_AppId', '')
        ]);
    }
}
