<?php


namespace Module\Vendor\Web\Controller;


use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Session;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;

class SessionController extends Controller
{
    public function index()
    {
        $input = InputPackage::buildFromInput();
        $apiToken = $input->getTrimString('api_token');
        $redirect = $input->getTrimString('redirect');
        BizException::throwsIfEmpty('api_token 为空', $apiToken);
        BizException::throwsIfEmpty('redirect 为空', $redirect);
        BizException::throwsIf('session 初始化失败', !Session::isValidId($apiToken));
        Session::setId($apiToken);
        Session::start();
        return Response::redirect($redirect);
    }
}
