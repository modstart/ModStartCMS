<?php


namespace ModStart\Layout;


use ModStart\Core\Util\IdUtil;
use ModStart\Field\AbstractField;

class LayoutSeparator extends AbstractField
{
    protected $isLayoutField = true;

    public function __construct($label)
    {
        parent::__construct(IdUtil::generate('LayoutSeparator'), [
            $label
        ]);
    }

}
