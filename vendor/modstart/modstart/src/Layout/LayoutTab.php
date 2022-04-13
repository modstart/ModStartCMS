<?php


namespace ModStart\Layout;


use ModStart\Core\Util\IdUtil;
use ModStart\Field\AbstractField;

class LayoutTab extends AbstractField
{
    protected $isLayoutField = true;
    private $layoutClosure = null;
    private $tabTitles = [];

    /**
     * LayoutGrid constructor.
     */
    public function __construct($closure)
    {
        parent::__construct(IdUtil::generate('LayoutTab'));
        $this->layoutClosure = $closure;
    }

    public function postSetup()
    {
        $this->context->html($this->column() . '_end')->html('<div data-layout-tab="' . $this->column() . '"><div class="ub-nav-tab"></div><div>')->plain();
        call_user_func($this->layoutClosure, $this);
        $titles = array_map(function ($title) {
            return '<a href="javascript:;">' . $title . '</a>';
        }, $this->tabTitles);
        $titles = json_encode(join('', $titles));
        $column = $this->column();
        $scripts = <<< JS
<script>
$(function(){
    var tab = $('[data-layout-tab=${column}]');
    tab.find('.ub-nav-tab').html(${titles});
    var tabAs = tab.find('.ub-nav-tab > a');
    var active = function(index){
        tabAs.removeClass('active').eq(index).addClass('active');
        tab.find('.ub-nav-tab-body').addClass('hidden').eq(index).removeClass('hidden');
    };
    active(0);
    tab.on('click','a',function(){
        active(tabAs.index(this));
    });
});
</script>
JS;
        $this->context->html($this->column() . '_end')->html('</div></div>' . $scripts)->plain();
    }


    /**
     * @param string $title
     * @param \Closure $closure
     *
     * @example
     * $closure = function ($builder) { }
     */
    public function tab($title, $closure)
    {
        $this->tabTitles[] = $title;
        $this->context->html($this->column() . '_end')->html('<div class="ub-nav-tab-body hidden">')->plain();
        call_user_func($closure, $this->context);
        $this->context->html($this->column() . '_end')->html('</div>')->plain();
    }

    public function render()
    {
        return '';
    }

}
