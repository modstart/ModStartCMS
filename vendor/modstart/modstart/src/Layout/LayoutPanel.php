<?php


namespace ModStart\Layout;


use ModStart\Core\Util\IdUtil;
use ModStart\Field\AbstractField;

class LayoutPanel extends AbstractField
{
    protected $isLayoutField = true;
    private $layoutClosure = null;
    private $panelTitle;

    /**
     * LayoutGrid constructor.
     */
    public function __construct($title, $arguments = [])
    {
        parent::__construct(IdUtil::generate('LayoutPanel'));
        $this->panelTitle = $title;
        $this->layoutClosure = $arguments[0];
    }

    public function postSetup()
    {
        $this->context->html($this->column() . '_end')->html('<div class="ub-panel"><div class="head"><div class="title">' . $this->panelTitle . '</div></div><div class="body">')->plain();
        call_user_func($this->layoutClosure, $this->context());
        $this->context->html($this->column() . '_end')->html('</div></div>')->plain();
    }

    public function render()
    {
        return '';
    }

}
