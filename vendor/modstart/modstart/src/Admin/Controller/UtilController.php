<?php


namespace ModStart\Admin\Controller;


use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Session;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\UrlUtil;

class UtilController extends Controller
{
    public function frame()
    {
        $input = InputPackage::buildFromInput();
        Session::put('_adminFrameLeftToggle', $input->getBoolean('frameLeftToggle'));
        return Response::jsonSuccess();
    }

    public function switchLang()
    {
        $input = InputPackage::buildFromInput();
        $redirect = $input->getTrimString('redirect', modstart_admin_url(''));
        $lang = $input->getTrimString('lang');
        L_locale($lang);
        // 防止开放重定向：仅允许站内安全地址
        if (!UrlUtil::isSafeRedirect($redirect)) {
            $redirect = modstart_admin_url('');
        }
        return Response::redirect($redirect);
    }
}
