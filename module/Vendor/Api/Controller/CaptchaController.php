<?php


namespace Module\Vendor\Api\Controller;

use Illuminate\Routing\Controller;
use ModStart\Core\Input\Response;
use ModStart\Misc\Captcha\CaptchaFacade;

class CaptchaController extends Controller
{
    public function image()
    {
        $captcha = CaptchaFacade::create('default');
        return Response::generate(0, 'ok', [
            'image' => 'data:image/png;base64,' . base64_encode($captcha->getOriginalContent()),
        ]);
    }
}
