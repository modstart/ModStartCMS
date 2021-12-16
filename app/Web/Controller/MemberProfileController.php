<?php


namespace App\Web\Controller;


use ModStart\App\Web\Layout\WebConfigBuilder;
use ModStart\Form\Form;
use Module\Member\Auth\MemberUser;
use Module\Member\Support\MemberLoginCheck;
use Module\Member\Type\Gender;
use Module\Member\Web\Controller\MemberFrameController;

class MemberProfileController extends MemberFrameController implements MemberLoginCheck
{
    /** @var \App\Api\Controller\MemberProfileController */
    private $api;

    /**
     * MemberProfileController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->api = app(\App\Api\Controller\MemberProfileController::class);
    }

    public function index(WebConfigBuilder $builder)
    {
        $builder->pageTitle('基本资料');
        $builder->page()->view($this->viewMemberFrame);
        $builder->text('username', '用户名')->readonly(true);
        $builder->radio('gender', '性别')->optionType(Gender::class);
        $builder->textarea('signature', '签名');
        return $builder->perform(MemberUser::user(), function (Form $form) {
            return $this->api->basic($form->dataForming());
        });
    }
}
