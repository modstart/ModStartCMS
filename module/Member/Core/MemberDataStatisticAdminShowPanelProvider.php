<?php


namespace Module\Member\Core;


use ModStart\Core\Util\FileUtil;
use ModStart\Form\Form;
use Module\Member\Model\MemberDataStatistic;
use Module\Member\Provider\MemberAdminShowPanel\AbstractMemberAdminShowPanelProvider;

class MemberDataStatisticAdminShowPanelProvider extends AbstractMemberAdminShowPanelProvider
{
    const NAME = 'MemberDataStatistic';

    public function name()
    {
        return self::NAME;
    }

    public function title()
    {
        return '存储';
    }

    public function render($memberUser, $param = [])
    {
        $record = MemberDataStatistic::getCreateMemberUser($memberUser['id']);
        $form = Form::make('');
        $form->number('sizeLimit', '大小限制')->help('单位MB');
        $form->display('sizeUsed', '已使用大小')->addable(true);
        $form->hidden('memberUserId', '用户ID')->addable(true);
        $item = [];
        $item['memberUserId'] = $memberUser['id'];
        $item['sizeLimit'] = $record['sizeLimit'];
        $item['sizeUsed'] = FileUtil::formatByte($record['sizeUsed']);
        $form->item($item)->fillFields();
        $form->showReset(false);
        $form->formUrl(modstart_admin_url('member/config/data_statistic'));
        return $form->render();
    }

}
