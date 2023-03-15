<?php


namespace ModStart\Field;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Util\CRUDUtil;
use ModStart\Core\Util\IdUtil;
use ModStart\Detail\Detail;
use ModStart\Field\Type\FieldRenderMode;
use ModStart\Form\Form;
use ModStart\Grid\Grid;
use ModStart\ModStart;
use ModStart\Support\Concern\HasFluentAttribute;

/**
 * Class AbstractField
 * @package ModStart\Field
 *
 * @method AbstractField|mixed listable($value = null)
 *
 * @method AbstractField|mixed addable($value = null)
 *
 * @method AbstractField|mixed editable($value = null)
 *
 * @method AbstractField|mixed showable($value = null)
 *
 * @method AbstractField|mixed sortable($value = null)
 *
 * @method AbstractField|mixed renderMode($value = null)
 *
 * form模式：默认值
 * @method AbstractField|mixed defaultValue($value = null)
 *
 * form模式：提示文字
 * @method AbstractField|mixed placeholder($value = null)
 *
 * form模式：只读
 * @method AbstractField|mixed readonly($value = null)
 *
 * form模式：帮助文字，样式
 * @method AbstractField|mixed help($value = null)
 * grid|form|detail模式：字段提示
 * @method AbstractField|mixed tip($value = null)
 * @method AbstractField|mixed styleFormField($value = null)
 *
 * grid模式：字段宽度
 * @method AbstractField|mixed width($value = null)
 *
 * @method AbstractField|mixed hookFormatValue($value = null)
 * @method AbstractField|mixed hookValueUnserialize($value = null)
 *
 * > $value = function ($value, AbstractField $field) { return $value; }
 * @method AbstractField|mixed hookValueSerialize($value = null)
 *
 * grid|form|detail模式：渲染自定义回调
 * > $value = function(AbstractField $field, $item, $index){ return $item->title; }
 * @method AbstractField|mixed hookRendering($value = null)
 *
 * @method AbstractField|mixed isLayoutField($vlaue = null)
 * @method AbstractField|mixed isCustomField($vlaue = null)
 * @method AbstractField|mixed gridFixed($vlaue = null)
 * @method AbstractField|mixed gridEditable($vlaue = null)
 *
 * >>>>>> 数据流转换流程 >>>>>>
 *
 * grid.request
 * -> repository->get
 * -> hookValueUnserialize(function($value, AbstractField $field){return $value;})
 * -> unserializeValue($value, AbstractField $field)
 * -> hookFormatValue(function($value, AbstractField $field){ return $value;})
 * -> view->render
 *
 * form.add
 * -> view->render
 *
 * form.addRequest
 * -> prepareInput($value, $dataSubmitted)
 * -> serializeValue($value, $dataSubmitted)
 * -> hookValueSerialize(function($value, AbstractField $field){return $value;})
 * -> repository->add
 *
 * form.formRequest
 * -> prepareInput($value, $dataSubmitted)
 * -> serializeValue($value, $dataSubmitted)
 * -> hookValueSerialize(function($value, AbstractField $field){return $value;})
 * -> repository->add
 *
 * form.edit
 * -> repository->editing
 * -> hookValueUnserialize(function($value, AbstractField $field){return $value;})
 * -> unserializeValue($value, AbstractField $field)
 * -> hookFormatValue(function($value, AbstractField $field){ return $value;})
 * -> view->render
 *
 * form.editRequest
 * -> prepareInput($value, $dataSubmitted)
 * -> serializeValue($value, $dataSubmitted)
 * -> hookValueSerialize(function($value, AbstractField $field){return $value;})
 * -> repository->edit
 *
 * detail.show
 * -> repository->show
 * -> hookValueUnserialize(function($value, AbstractField $field){return $value;})
 * -> unserializeValue($value, AbstractField $field)
 * -> hookFormatValue(function($value, AbstractField $field){ return $value;})
 * -> view->render
 *
 * form.deleteRequest
 * -> repository->deleting
 * -> repository->delete
 *
 */
class AbstractField implements Renderable
{
    use HasFluentAttribute;

    protected static $css = [];
    protected static $js = [];
    private $script = '';

    /** @var Form|Grid|Detail $context */
    protected $context;

    private $id;
    private $name;
    private $column;
    protected $label;
    protected $value = null;
    protected $fixedValue = null;
    protected $rules = [];
    protected $view = null;
    protected $variables = [];
    /**
     * 当前条目
     * grid模式：当前条目
     * form模式：edit当前编辑条目
     *
     * @var Model|\stdClass
     */
    private $item;

    private $fluentAttributes = [
        'listable',
        'addable',
        'editable',
        'showable',
        'sortable',
        'renderMode',
        'defaultValue',
        'placeholder',
        'help',
        'tip',
        'width',
        'styleFormField',
        'readonly',
        'hookFormatValue',
        'hookValueUnserialize',
        'hookValueSerialize',
        'hookRendering',
        'isLayoutField',
        'isCustomField',
        'gridFixed',
        'gridEditable',
    ];
    /**
     * 字段渲染模式，默认为 add，请查看 @see FieldRenderMode
     * @var string
     */
    protected $listable = true;
    protected $addable = true;
    protected $editable = true;
    protected $showable = true;
    protected $sortable = false;
    private $renderMode;
    private $defaultValue = null;
    private $placeholder = null;
    private $help = null;
    private $tip = null;
    private $styleFormField = null;
    protected $width = '';
    private $readonly = false;
    /**
     * 格式化值
     * @var \Closure
     */
    private $hookFormatValue;
    /**
     * 将DB中的值反序列化
     * @var \Closure
     */
    private $hookValueUnserialize;
    /**
     * 将值序列化存储在DB中
     * @var \Closure
     */
    private $hookValueSerialize;
    private $hookRendering;
    /**
     * 是否为布局类
     * @var bool
     */
    protected $isLayoutField = false;
    /**
     * 是否为自定义字段（自定义字段不参与Form中的addRequest、editRequest计算）
     * @var bool
     */
    protected $isCustomField = false;
    /**
     * 数据表示模式下浮动布局
     * @var null|left|right
     */
    private $gridFixed = null;
    /**
     * @var bool 行内编辑
     */
    private $gridEditable = false;

    public static function getAssets()
    {
        return [
            'css' => static::$css,
            'js' => static::$js,
        ];
    }

    public function __construct($column, $arguments = [])
    {
        $this->id = IdUtil::generate('Field');
        $this->column = $column;
        if (isset($arguments[0])) {
            $this->label = $arguments[0];
        } else {
            $this->label = null;
        }
        $this->setup();
    }

    protected function setup()
    {

    }

    public function postSetup()
    {

    }

    public function context($context = null)
    {
        if (null === $context) {
            return $this->context;
        }
        $this->context = $context;
        return $this->context;
    }

    public function rules($rule = null)
    {
        if (is_null($rule)) {
            return $this->rules;
        }
        // echo json_encode([$this->column, $this->rules]) . "\n";
        if (is_array($rule)) {
            $rule = array_filter($rule);
            $this->rules = array_merge($this->rules, $rule);
        } else {
            if (!empty($rule)) {
                $this->rules[] = $rule;
            }
        }
        // $rules = array_filter(explode('|', "{$this->rules}|$rules"));
        // $this->rules = implode('|', $rules);
        return $this;
    }

    /**
     * @return $this|array
     */
    public function required()
    {
        return $this->rules('required');
    }

    /**
     * @return $this|array
     */
    public function ruleRegex($regex)
    {
        return $this->rules('regex:' . $regex);
    }

    /**
     * 匹配 Url
     * @return $this|array
     */
    public function ruleUrl()
    {
        return $this->ruleRegex('/^https?:\/\//');
    }

    /**
     * @return $this|array
     */
    public function ruleUnique($table, $field = null)
    {
        if (null === $field) {
            $field = $this->column();
        }
        return $this->rules('unique:' . $table . ',' . $field . ',' . CRUDUtil::id());
    }

    public function id()
    {
        return $this->id;
    }

    public function column()
    {
        return $this->column;
    }

    private function formatName($column)
    {
        if (is_string($column)) {
            $names = explode('.', $column);

            if (count($names) == 1) {
                return $names[0];
            }

            $name = array_shift($names);
            foreach ($names as $piece) {
                $name .= "[$piece]";
            }
            return $name;
        }
    }

    public function name($value = null)
    {
        if (null === $value) {
            if (empty($this->name)) {
                return $this->formatName($this->column);
            }
            return $this->name;
        }
        $this->name = $value;
        return $this;
    }

    public function label($value = null)
    {
        if (null === $value) {
            return $this->label;
        }
        $this->label = $value;
        return $this;
    }

    public function value($value = null)
    {
        if (null === $value) {
            if (null === $this->value) {
                return $this->defaultValue();
            }
            return $this->value;
        }
        $this->value = $value;
        return $this;
    }

    public function fixedValue($fixedValue = null)
    {
        if (null === $fixedValue) {
            return $this->fixedValue;
        }
        $this->fixedValue = $fixedValue;
        return $this;
    }

    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @param null $item
     * @return $this|Model|\stdClass
     */
    public function item($item = null)
    {
        if (null === $item) {
            return $this->item;
        }
        $this->item = $item;
        return $this;
    }

    /**
     * 数据反序列化
     * @param $value
     * @param $model
     * @return mixed
     */
    public function unserializeValue($value, AbstractField $field)
    {
        return $value;
    }

    /**
     * 值序列化
     * @param $value
     * @param $model
     * @return mixed
     */
    public function serializeValue($value, $model)
    {
        return $value;
    }

    /**
     * 转换从view到提交值
     * @param mixed $value
     * @param array $dataSubmitted
     * @return mixed
     */
    public function prepareInput($value, $dataSubmitted)
    {
        return $value;
    }

    /**
     * 填充字段值，data包含当前记录
     * @param Arrayable|array $item
     */
    public function fill($item)
    {
        if ($this->isLayoutField()) {
            return;
        }
        $this->item = $item;
        if (is_array($this->column)) {
            foreach ($this->column as $key => $column) {
                $value = array_get($item, $column);
                if ($this->hookValueUnserialize) {
                    $value = call_user_func($this->hookValueUnserialize, $value, $this);
                }
                $value = $this->unserializeValue($value, $this);
                if ($this->hookFormatValue) {
                    $value = call_user_func($this->hookFormatValue, $value, $this);
                }
                $this->value[$key] = $value;
            }
            return;
        }
        if (is_array($item)) {
            $value = array_get($item, $this->column);
        } else {
            if (str_contains($this->column, '.')) {
                $value = ModelUtil::traverse($item, $this->column);
            } else {
                $value = isset($item->{$this->column}) ? $item->{$this->column} : null;
            }
            // echo $this->column . " - " . json_encode($item) . "\n";
        }
        if ($this->hookValueUnserialize) {
            $value = call_user_func($this->hookValueUnserialize, $value, $this);
        }
        $value = $this->unserializeValue($value, $this);
        if ($this->hookFormatValue) {
            $value = call_user_func($this->hookFormatValue, $value, $this);
        }
        $this->value = $value;
    }

    /**
     * Add variables to field view.
     *
     * @param array $variables
     *
     * @return $this
     */
    public function addVariables(array $variables = [])
    {
        $this->variables = array_merge($this->variables, $variables);
        return $this;
    }

    public function getVariable($key, $default = null)
    {
        if (isset($this->variables[$key])) {
            return $this->variables[$key];
        }
        return $default;
    }

    /**
     * Get validator for this field.
     *
     * @param array $input
     *
     * @return bool|Validator
     */
    public function getValidator(array $input)
    {
        $rules = $attributes = [];
        if (!$fieldRules = $this->rules()) {
            return false;
        }

        if (is_string($this->column)) {
            if (!array_has($input, $this->column)) {
                return false;
            }
            $rules[$this->column] = $fieldRules;
            $attributes[$this->column] = $this->label;
        }

        if (is_array($this->column)) {
            foreach ($this->column as $key => $column) {
                if (!array_key_exists($column, $input)) {
                    continue;
                }
                $input[$column . $key] = array_get($input, $column);
                $rules[$column . $key] = $fieldRules;
                $attributes[$column . $key] = $this->label . "[$column]";
            }
        }
        return Validator::make($input, $rules, [], $attributes);
    }


    protected function variables()
    {
        // echo $this->column . " : " . json_encode($this->value()) . "\n";
        return array_merge($this->fluentAttributeVariables(), $this->variables, [
            'id' => $this->id,
            'name' => $this->name(),
            'value' => $this->value(),
            'fixedValue' => $this->fixedValue(),
            'label' => $this->label,
            'column' => $this->column,
            'placeholder' => $this->placeholder(),
            'rules' => $this->rules,
        ]);
    }

    public function view($mode = '', $name = null)
    {
        if (!empty($this->view)) {
            return $this->view . ($mode ? '-' . $mode : '');
        }
        if (null === $name) {
            $class = explode('\\', get_called_class());
            $name = lcfirst(end($class));
        }
        return 'modstart::core.field.' . $name . ($mode ? '-' . $mode : '');
    }

    public function render()
    {
        return $this->renderView($this, $this->item);
    }

    public function renderView(AbstractField $field, $item, $index = 0)
    {
        try {
            $column = $field->column();
            if ($this->hookRendering instanceof \Closure) {
                $ret = call_user_func($this->hookRendering, $this, $item, $index);
                if (null !== $ret) {
                    if ($ret instanceof AutoRenderedFieldValue) {
                        return view('modstart::core.field.autoRenderedField-' . $this->renderMode, [
                            'label' => $this->label,
                            'tip' => $this->tip,
                            'help' => $this->help,
                            'value' => $ret->getValue(),
                        ])->render();
                    }
                    return $ret;
                }
            }
            ModStart::script($this->script);
            switch ($this->renderMode) {
                case FieldRenderMode::FORM:
                    return View::make($this->view(), $this->variables())->render();
                case FieldRenderMode::DETAIL:
                    if (view()->exists($view = $this->view($this->renderMode))) {
                        return View::make($view, $this->variables())->render();
                    }
                    return View::make($this->view($this->renderMode, 'text'), $this->variables())->render();
                case FieldRenderMode::GRID:
                    if (view()->exists($view = $this->view($this->renderMode))) {
                        // echo json_encode($this->variables())."\n";
                        return View::make($view, array_merge([
                            'item' => $item,
                            '_index' => $index,
                        ], $this->variables()))->render();
                    }
                    if (is_array($item->{$column})) {
                        return join(', ', $item->{$column});
                    }
                    if (str_contains($column, '.')) {
                        $value = (string)ModelUtil::traverse($item, $column);
                        // echo $column . ' - ' . json_encode($value) . "\n";
                    } else {
                        $value = (string)$item->{$column};
                    }
                    return htmlspecialchars($value);
            }
        } catch (\Throwable $e) {
            Log::error('Field renderMode error - ' . $e->getMessage() . ' - ' . $e->getTraceAsString());
            return new \Exception('Field renderMode error');
        }
        throw new \Exception('Field renderMode not exists');
    }

    public function __call($method, $arguments)
    {
        if ($this->isFluentAttribute($method)) {
            return $this->fluentAttribute($method, $arguments);
        }
        throw new \Exception('AbstractField __call error : ' . $method);
    }

}
