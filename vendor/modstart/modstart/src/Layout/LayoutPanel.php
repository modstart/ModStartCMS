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
        if (!is_array($title)) {
            $title = [
                'title' => $title,
                'formLineLabel' => null,
            ];
        }
        $this->panelTitle = $title;
        $this->layoutClosure = $arguments[0];
    }

    public function postSetup()
    {
        if ($this->panelTitle['formLineLabel']) {
            $this->context->html($this->column() . '_form_line_start')
                ->html('<div class="line"><div class="label">'
                    . htmlspecialchars($this->panelTitle['formLineLabel']) . '</div><div class="field">')
                ->plain();
        }
        if ($this->panelTitle['title']) {
            $this->context->html($this->column() . '_start')
                ->html('<div class="ub-panel"><div class="head"><div class="title">'
                    . $this->panelTitle['title'] . '</div></div><div class="body">')->plain();
        } else {
            $this->context->html($this->column() . '_start')
                ->html('<div class="ub-content-box">')->plain();
        }
        call_user_func($this->layoutClosure, $this->context());
        if ($this->panelTitle['title']) {
            $this->context->html($this->column() . '_end')->html('</div></div>')->plain();
        } else {
            $this->context->html($this->column() . '_end')->html('</div>')->plain();
        }
        if ($this->panelTitle['formLineLabel']) {
            $this->context->html($this->column() . '_form_line_end')
                ->html('</div></div>')
                ->plain();
        }
    }

    public function render()
    {
        return '';
    }

}
