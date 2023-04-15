<?php

namespace ModStart\Grid\Filter;

use Illuminate\Support\Facades\View;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Util\IdUtil;
use ModStart\Grid\Filter;
use ModStart\Grid\Filter\Field\Text;
use ModStart\Grid\Grid;
use ModStart\Grid\GridFilter;
use ModStart\Support\Concern\HasFluentAttribute;

/**
 * Class AbstractFilter
 * @package ModStart\Grid\Filter
 *
 *
 * @method AbstractFilter hookRendering($value = null)
 * > $value = function(AbstractFilter $filter){  }
 */
abstract class AbstractFilter
{
    use HasFluentAttribute;

    private $fluentAttributes = [
        'hookRendering',
    ];
    private $hookRendering;

    /**
     * @var Grid
     */
    private $grid;

    /**
     * @var
     */
    protected $id;
    /**
     * 名称
     * @var string
     */
    protected $label;
    /**
     * 字段名
     * @var string
     */
    protected $column;
    /**
     * field
     * @var Filter\Field\AbstractFilterField
     */
    protected $field;
    /**
     * @var
     */
    protected $defaultValue;
    /**
     * Query for filter.
     *
     * @var string
     */
    protected $query = 'where';
    /**
     * @var GridFilter
     */
    private $tableFilter;
    /**
     * 是否自动展开
     * @var bool
     */
    private $autoHide = false;

    /**
     * AbstractFilter constructor.
     *
     * @param $column
     * @param string $label
     */
    public function __construct($column, $label = '')
    {
        $this->id = IdUtil::generate('GridFilter');
        $this->column = $column;
        $this->label = $label;

        $this->field = new Text($this);
        $this->field->label($this->label);
    }

    public function name()
    {
        $class = explode('\\', get_called_class());
        return lcfirst(end($class));
    }

    /**
     * @param GridFilter $filter
     */
    public function setTableFilter(GridFilter $filter)
    {
        $this->tableFilter = $filter;
    }

    public function condition($searchInfo)
    {
        if (isset($searchInfo['eq']) && $searchInfo['eq'] !== '') {
            return $this->buildCondition($this->column, '=', $searchInfo['eq']);
        }
        return null;
    }

    /**
     * 获取字段
     *
     * @return $this|Filter\Field\AbstractFilterField|Filter\Field\Select|Filter\Field\Datetime
     */
    public function field($value = null)
    {
        if (null === $value) {
            return $this->field;
        }
        $this->field = $value;
        return $this;
    }

    /**
     * 筛选条件默认值
     * @param $value
     * @return $this
     */
    public function defaultValue($value)
    {
        $this->defaultValue = $value;
        return $this;
    }

    /**
     * 是否自动收缩
     * @param $autoHide
     * @return $this|boolean
     */
    public function autoHide($autoHide = null)
    {
        if (null === $autoHide) {
            return $this->autoHide;
        }
        $this->autoHide = $autoHide;
        return $this;
    }

    /**
     * 筛选字段
     *
     * @return string
     */
    public function column()
    {
        return $this->column;
    }

    /**
     * Build conditions of filter.
     *
     * @return array|mixed
     */
    protected function buildCondition()
    {
        $column = explode('.', $this->column);
        if (count($column) == 1 || $this->grid->isDynamicModel()) {
            return [$this->query => func_get_args()];
        }
        return call_user_func_array([$this, 'buildRelationCondition'], func_get_args());
    }

    /**
     * Build query condition of model relation.
     *
     * @return array
     */
    protected function buildRelationCondition()
    {
        $args = func_get_args();
        list($relation, $args[0]) = explode('.', $this->column);
        return ['whereHas' => [$relation, function ($relation) use ($args) {
            call_user_func_array([$relation, $this->query], $args);
        }]];
    }

    /**
     * Variables for filter view.
     *
     * @return array
     */
    public function variables()
    {
        $variables = [
            'id' => $this->id,
            'column' => $this->column,
            'label' => $this->label,
            'field' => $this->field,
            'defaultValue' => $this->defaultValue,
            'autoHide' => $this->autoHide,
        ];
        $variables = array_merge($variables, $this->field->variables());
        return $variables;
    }

    /**
     * Render this filter.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        if ($this->hookRendering instanceof \Closure) {
            $ret = call_user_func($this->hookRendering, $this);
            if (null !== $ret) {
                return $ret;
            }
        }
        $class = explode('\\', get_called_class());
        $fieldClass = explode('\\', get_class($this->field));
        $view = 'modstart::core.grid.filter.'
            . lcfirst(end($class)) . '-' . lcfirst(end($fieldClass));
        return View::make($view, $this->variables())->render();
    }

    /**
     * @param null $grid
     * @return $this|Grid
     */
    public function grid($grid = null)
    {
        if (null === $grid) {
            return $this->grid;
        }
        $this->grid = $grid;
        return $this;
    }

    public function __call($name, $arguments)
    {
        if ($this->isFluentAttribute($name)) {
            return $this->fluentAttribute($name, $arguments);
        }
        throw new \Exception('AbstractFilter __call error : ' . $name);
    }

}
