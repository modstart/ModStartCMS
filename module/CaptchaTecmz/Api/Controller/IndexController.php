<?php


namespace Module\CaptchaTecmz\Api\Controller;


use Illuminate\Routing\Controller;
use ModStart\Core\Input\Response;

class IndexController extends Controller
{
    public function info()
    {
        $config = modstart_config();
        return Response::generateSuccessData([
            'appId' => $config->getWithEnv('CaptchaTecmz_AppId', '')
        ]);
    }
}
