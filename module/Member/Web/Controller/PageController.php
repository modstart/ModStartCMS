<?php


namespace Module\Member\Web\Controller;


use ModStart\Module\ModuleBaseController;

class PageController extends ModuleBaseController
{
    public function agreement()
    {
        return $this->view('member.page', [
            'pageTitle' => modstart_config('Member_AgreementTitle'),
            'pageContent' => modstart_config('Member_AgreementContent'),
        ]);
    }

    public function privacy()
    {
        return $this->view('member.page', [
            'pageTitle' => modstart_config('Member_PrivacyTitle'),
            'pageContent' => modstart_config('Member_PrivacyContent'),
        ]);
    }

    public function appeal()
    {
        return $this->view('member.page', [
            'pageTitle' => modstart_config('Member_AppealTitle'),
            'pageContent' => modstart_config('Member_AppealContent'),
        ]);
    }

    public function vip()
    {
        return $this->view('member.page', [
            'pageTitle' => modstart_config('Member_VipTitle', '会员协议'),
            'pageContent' => modstart_config('Member_VipContent'),
        ]);
    }
}
