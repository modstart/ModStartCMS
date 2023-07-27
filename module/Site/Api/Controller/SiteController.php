<?php


namespace Module\Site\Api\Controller;


use Illuminate\Routing\Controller;
use ModStart\Core\Assets\AssetsUtil;
use ModStart\Core\Input\Response;

class SiteController extends Controller
{
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
