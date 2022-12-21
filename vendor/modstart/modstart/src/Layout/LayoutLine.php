<?php


namespace ModStart\Layout;


use ModStart\Core\Util\IdUtil;
use ModStart\Field\AbstractField;

class LayoutLine extends AbstractField
{
    protected $isLayoutField = true;
    private $layoutClosure = null;

    /**
     * LayoutGrid constructor.
     */
    public function __construct($title, $arguments = [])
    {
        parent::__construct(IdUtil::generate('LayoutPanel'));
        $this->label = $title;
        $this->layoutClosure = $arguments[0];
    }

    public function postSetup()
    {
        $this->context->html($this->column() . '_start')->html('<div class="line"><div class="label">' . $this->label . ':</div><div class="field">')->plain();
        call_user_func($this->layoutClosure, $this->context());
        $this->context->html($this->column() . '_end')->html('</div></div>')->plain();
    }

    public function render()
    {
        return '';
    }

}
