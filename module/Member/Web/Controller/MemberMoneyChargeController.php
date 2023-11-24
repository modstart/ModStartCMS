<?php


namespace Module\Member\Web\Controller;


use Module\Member\Support\MemberLoginCheck;
use ModStart\Core\Exception\BizException;

class MemberMoneyChargeController extends MemberFrameController implements MemberLoginCheck
{
    /** @var \Module\Member\Api\Controller\MemberMoneyChargeController */
    private $api;

    /**
     * MemberMoneyChargeController constructor.
     * @param \Module\Member\Api\Controller\MemberMoneyChargeController $api
     */
    public function __construct(\Module\Member\Api\Controller\MemberMoneyChargeController $api)
    {
        parent::__construct();
        $this->api = $api;
    }

    public function index()
    {
        BizException::throwsIf('钱包充值未开启', !modstart_config('Member_MoneyChargeEnable', false));
        return $this->view('memberMoneyCharge.index', [
            'pageTitle' => '钱包充值',
        ]);
    }
}
