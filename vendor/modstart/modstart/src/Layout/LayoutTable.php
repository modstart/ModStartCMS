<?php


namespace ModStart\Layout;


use ModStart\Core\Util\IdUtil;
use ModStart\Field\AbstractField;

class LayoutTable extends AbstractField
{
    protected $isLayoutField = true;
    private $layoutClosure = null;

    /**
     * LayoutGrid constructor.
     */
    public function __construct($closure)
    {
        parent::__construct(IdUtil::generate('LayoutTable'));
        $this->layoutClosure = $closure;
    }

    public function postSetup()
    {
        $this->context->html($this->column() . '_end')->html('<table class="ub-table border ub-content-bg">')->plain();
        call_user_func($this->layoutClosure, $this);
        $this->context->html($this->column() . '_end')->html('</table>')->plain();
    }


    /**
     * @param $closure
     *
     * @example
     * $closure = function ($builder) { }
     */
    public function layoutRow($closure)
    {
        $this->context->html($this->column() . '_end')->html('<tr>')->plain();
        call_user_func($closure, $this);
        $this->context->html($this->column() . '_end')->html('</tr>')->plain();
    }

    /**
     * @param $closure
     *
     * @example
     * $closure = function ($builder) { }
     */
    public function layoutCol($closure)
    {
        $this->context->html($this->column() . '_end')->html('<td>')->plain();
        call_user_func($closure, $this->context);
        $this->context->html($this->column() . '_end')->html('</td>')->plain();
    }

    public function render()
    {
        return '';
    }

}
