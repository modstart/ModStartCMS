<?php


namespace Module\CaptchaTecmz\Web\Controller;


use Illuminate\Routing\Controller;
use ModStart\Core\Input\InputPackage;
use Module\Vendor\Tecmz\TecmzUtil;

class IndexController extends Controller
{
    public function verify()
    {
        $input = InputPackage::buildFromInput();
        return TecmzUtil::instance('CaptchaTecmz_')->captchaVerify(
            $input->getTrimString('action'),
            $input->getTrimString('key'),
            $input->getTrimString('data'),
            $input->getTrimString('runtime'),
            $input->getTrimString('types')
        );
    }
}
