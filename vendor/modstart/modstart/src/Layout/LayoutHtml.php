<?php


namespace ModStart\Layout;


use ModStart\Core\Util\IdUtil;
use ModStart\Field\AbstractField;

class LayoutHtml extends AbstractField
{
    protected $isLayoutField = true;

    public function __construct($label)
    {
        parent::__construct(IdUtil::generate('LayoutHtml'), [
            $label
        ]);
    }

}
