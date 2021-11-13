<?php


namespace Module\Member\Widget\Field;


use ModStart\Field\Image;


class MemberImage extends Image
{
    protected $view = 'modstart::core.field.image';

    protected function setup()
    {
        parent::setup();
        $this->addVariables(['server' => modstart_web_url('member_data/file_manager/image')]);
    }

}
