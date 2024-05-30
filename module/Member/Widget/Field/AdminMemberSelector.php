<?php


namespace Module\Member\Widget\Field;


use ModStart\Field\AbstractField;
use ModStart\Field\Text;
use ModStart\Field\Type\FieldRenderMode;
use Module\Member\Util\MemberCmsUtil;

class AdminMemberSelector extends Text
{
    protected $view = 'module::Member.View.field.adminUserSelector';

    protected function setup()
    {
        parent::setup();
        $this->addVariables([
            'server' => modstart_admin_url('member/select'),
        ]);
    }


    public function renderView(AbstractField $field, $item, $index = 0)
    {
        return parent::renderView($field, $item, $index);
    }

}
