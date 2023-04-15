<?php


namespace Module\Vendor\Web\Controller;


use Illuminate\Routing\Controller;
use ModStart\App\Web\Layout\WebPage;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Form\Form;
use Module\Vendor\Provider\ContentVerify\ContentVerifyBiz;

class ContentVerifyController extends Controller
{
    public function index(WebPage $page, $name)
    {
        $provider = ContentVerifyBiz::get($name);
        BizException::throwsIfEmpty('数据异常', $provider);
        $param = InputPackage::buildFromInputJson('param')->all();
        $form = Form::make('');
        $ret = $provider->buildForm($form, $param);
        if (null !== $ret) {
            return $ret;
        }
        return view('module::Vendor.View.contentVerify.index', [
            'content' => $form->render(),
            'pageTitle' => '审核 · ' . $provider->title(),
        ]);
    }
}
