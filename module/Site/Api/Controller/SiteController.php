<?php


namespace Module\Site\Api\Controller;


use Illuminate\Routing\Controller;
use ModStart\Core\Assets\AssetsUtil;
use ModStart\Core\Input\Response;

/**
 * Class SiteController
 * @package Module\Site\Api\Controller
 * @Api 站点信息
 */
class SiteController extends Controller
{
    /**
     * @Api 站点联系方式
     * @ApiDesc 获取站点联系邮箱、电话、地址与二维码
     * @ApiMethod post
     * @ApiResponseData {
     *   "email": "",
     *   "phone": "",
     *   "address": "",
     *   "qrcode": ""
     * }
     */
    public function contact()
    {
        $data = [];
        $data['email'] = modstart_config('Site_ContactEmail', '');
        $data['phone'] = modstart_config('Site_ContactPhone', '');
        $data['address'] = modstart_config('Site_ContactAddress', '');
        $data['qrcode'] = modstart_config('Site_ContactQrcode', '');
        if ($data['qrcode']) {
            $data['qrcode'] = AssetsUtil::fixFull($data['qrcode']);
        }
        return Response::generateSuccessData($data);
    }
}
