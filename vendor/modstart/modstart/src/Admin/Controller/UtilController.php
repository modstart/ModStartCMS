<?php


namespace ModStart\Admin\Controller;


use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Session;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;

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
        return Response::redirect($redirect);
    }
}
