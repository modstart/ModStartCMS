<?php

namespace ModStart\Grid\Filter;

use Illuminate\Support\Facades\View;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Util\IdUtil;
use ModStart\Grid\Filter;
use ModStart\Grid\Filter\Field\Text;
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
     * @var
     */
    protected $id;
    /**
     * Label of field.
     *
     * @var string
     */
    protected $label;
    /**
     * @var string
     */
    protected $column;
    /**
     * field
     * @var Filter\Field\AbstractFilterField
     */
    protected $field;
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
     * Get field object of filter.
     *
     * @return mixed|$this
     */
    public function field($value = null)
    {
        if (null === $value) {
            return $this->field;
        }
        $this->field = $value;
    }

    /**
     * Get column name of current filter.
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
        if (count($column) == 1) {
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
    private function variables()
    {
        $variables = [
            'id' => $this->id,
            'column' => $this->column,
            'label' => $this->label,
            'field' => $this->field,
        ];
        if (method_exists($this->field, 'variables')) {
            $variables = array_merge($variables, $this->field()->variables());
        }
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

    public function __call($name, $arguments)
    {
        if ($this->isFluentAttribute($name)) {
            return $this->fluentAttribute($name, $arguments);
        }
        throw new \Exception('AbstractFilter __call error : ' . $name);
    }

}
