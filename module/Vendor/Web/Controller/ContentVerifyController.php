<?php


namespace Module\Vendor\Web\Controller;


use Illuminate\Routing\Controller;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
use ModStart\Form\Form;
use Module\Vendor\Provider\ContentVerify\ContentVerifyBiz;

class ContentVerifyController extends Controller
{
    public function index($name)
    {
        $provider = ContentVerifyBiz::getByName($name);
        BizException::throwsIfEmpty('数据异常', $provider);
        $param = InputPackage::buildFromInputJson('param')->all();
        if (Request::isPost()) {
            $input = InputPackage::buildFromInput();
            if (!empty($param['_action'])) {
                switch ($param['_action']) {
                    case 'pass':
                        $provider->verifyPassProcess($param);
                        return Response::generate(0, null, null, '[reload]');
                    case 'reject':
                        $param['_reason'] = $input->getTrimString('_reason');
                        $provider->verifyRejectProcess($param);
                        return Response::generate(0, null, null, '[reload]');
                    default:
                        BizException::throws('未知操作 - ' . $param['_action']);
                }
            }
        }
        $form = Form::make('');
        $ret = $provider->buildForm($form, $param);
        if (null !== $ret) {
            return $ret;
        }
        return view('module::Vendor.View.provider.contentVerify.index', [
            'content' => $form->render(),
            'pageTitle' => '审核 · ' . $provider->title(),
        ]);
    }
}
