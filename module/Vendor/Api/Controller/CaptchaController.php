<?php


namespace Module\Vendor\Api\Controller;

use Illuminate\Routing\Controller;
use ModStart\Core\Input\Response;
use ModStart\Misc\Captcha\CaptchaFacade;

/**
 * Class CaptchaController
 * @package Module\Vendor\Api\Controller
 * @Api 验证码
 */
class CaptchaController extends Controller
{
    /**
     * @Api 获取验证码图片
     * @ApiDesc 获取 base64 编码的图形验证码图片
     * @ApiMethod post
     * @ApiResponseData {
     *   "code": 0,
     *   "msg": "ok",
     *   "data": {
     *     "image": "data:image/png;base64,xxx"
     *   }
     * }
     */
    public function image()
    {
        $captcha = CaptchaFacade::create('default');
        return Response::generate(0, 'ok', [
            'image' => 'data:image/png;base64,' . base64_encode($captcha->getOriginalContent()),
        ]);
    }
}
