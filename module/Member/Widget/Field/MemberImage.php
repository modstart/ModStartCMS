<?php


namespace Module\Member\Widget\Field;


use ModStart\Field\Image;

/**
 * 用户图片字段
 *
 * Class MemberImage
 * @package Module\Member\Widget\Field
 */
class MemberImage extends Image
{
    protected $view = 'modstart::core.field.image';

    protected function setup()
    {
        parent::setup();
        $this->addVariables(['server' => modstart_web_url('member_data/file_manager/image')]);
    }

}
