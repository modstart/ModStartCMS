<?php


namespace ModStart\Layout;


use ModStart\Core\Util\IdUtil;
use ModStart\Field\AbstractField;

class LayoutGrid extends AbstractField
{
    protected $isLayoutField = true;
    private $layoutClosure = null;

    /**
     * LayoutGrid constructor.
     */
    public function __construct($closure)
    {
        parent::__construct(IdUtil::generate('LayoutGrid'));
        $this->layoutClosure = $closure;
    }

    public function postSetup()
    {
        $this->context->html($this->column() . '_end')->html('<div class="row">')->plain();
        call_user_func($this->layoutClosure, $this);
        $this->context->html($this->column() . '_end')->html('</div>')->plain();
    }


    /**
     * @param $widths array|int
     * @param $closure \Closure
     *
     * @example
     * $closure = function ($builder) { }
     */
    public function layoutColumn($widths, $closure)
    {
        if (!is_array($widths)) {
            $widths = [$widths, 12];
        }
        $this->context->html($this->column() . '_end')->html('<div class="col-md-' . $widths[0] . ' col-' . $widths[1] . '">')->plain();
        call_user_func($closure, $this->context);
        $this->context->html($this->column() . '_end')->html('</div>')->plain();
    }

    public function render()
    {
        return '';
    }

}
