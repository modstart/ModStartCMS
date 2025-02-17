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
use ModStart\Support\Manager\FieldManager;

/**
 * Class AbstractField
 * @package ModStart\Field
 *
 * @method $this listable($value = null)
 *
 * @method $this addable($value = null)
 *
 * @method $this editable($value = null)
 *
 * @method $this formShowOnly($value = null)
 *
 * @method $this showable($value = null)
 *
 * @method $this sortable($value = null)
 *
 * @method $this renderMode($value = null)
 *
 * form模式：默认值
 * @method $this defaultValue($value = null)
 *
 * form模式：提示文字
 * @method $this placeholder($value = null)
 *
 * form模式：只读
 * @method $this readonly($value = null)
 *
 * form模式：帮助文字，样式
 * @method $this help($value = null)
 *
 * grid|form|detail模式：字段提示
 * @method $this tip($value = null)
 *
 * form模式：字段样式，或直接作用到 input, textarea 等元素上
 * @method $this styleFormField($value = null)
 *
 * grid模式：字段宽度
 * @method $this width($value = null)
 *
 * @method $this hookFormatValue($value = null)
 * @method $this hookValueUnserialize($value = null)
 *
 * > $value = function ($value, AbstractField $field) { return $value; }
 * @method $this hookValueSerialize($value = null)
 *
 * > $value = function ($itemId, AbstractField $field) {  }
 * @method $this hookValueSaved($value = null)
 *
 * grid|form|detail模式：渲染自定义回调
 * > $value = function(AbstractField $field, $item, $index){ return $item->title; }
 * @method $this hookRendering($value = null)
 *
 * @method $this isLayoutField($vlaue = null)
 * @method $this isCustomField($vlaue = null)
 * @method $this gridFixed($vlaue = null)
 * grid模式：是否启用快捷编辑
 * > $value = true
 * > $value = function(AbstractField $field, $item, $index){ return true; }
 * @method $this gridEditable($vlaue = null)
 *
 * >>>>>> 数据流转换流程 >>>>>>
 *
 * grid.request
 * -> repository->get
 * -> hookValueUnserialize(function($value, AbstractField $field){ return $value; })
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
 * -> hookValueSerialize(function($value, AbstractField $field){ return $value; })
 * -> repository->add
 *
 * form.formRequest
 * -> prepareInput($value, $dataSubmitted)
 * -> serializeValue($value, $dataSubmitted)
 * -> hookValueSerialize(function($value, AbstractField $field){ return $value; })
 * -> repository->add
 *
 * form.edit
 * -> repository->editing
 * -> hookValueUnserialize(function($value, AbstractField $field){ return $value; })
 * -> unserializeValue($value, AbstractField $field)
 * -> hookFormatValue(function($value, AbstractField $field){ return $value;})
 * -> view->render
 *
 * form.editRequest
 * -> prepareInput($value, $dataSubmitted)
 * -> serializeValue($value, $dataSubmitted)
 * -> hookValueSerialize(function($value, AbstractField $field){ return $value; })
 * -> repository->edit
 *
 * detail.show
 * -> repository->show
 * -> hookValueUnserialize(function($value, AbstractField $field){ return $value; })
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
    protected $script = '';

    /** @var Form|Grid|Detail $context */
    protected $context;

    protected $id;
    /**
     * @var string 表单name
     */
    protected $name;
    /**
     * @var string 数据表字段名
     */
    protected $column;
    /**
     * @var mixed|null 表单名称
     */
    protected $label;
    /**
     * @var null 字段值，null表示没有值，非null表示有值
     */
    protected $value = null;
    /**
     * @var null 默认值，null表示没有默认值，非null表示有默认值
     */
    protected $defaultValue = null;
    /**
     * @var null ?
     */
    protected $fixedValue = null;
    /**
     * @var array 校验规则，如['required']，表单模式下生效
     */
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
    protected $item;

    protected $fluentAttributes = [
        'listable',
        'addable',
        'editable',
        'formShowOnly',
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
        'hookValueSaved',
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
    protected $formShowOnly = false;
    protected $showable = true;
    protected $sortable = false;
    protected $renderMode;
    protected $placeholder = null;
    protected $help = null;
    protected $tip = null;
    protected $styleFormField = null;
    protected $width = '';
    protected $readonly = false;
    /**
     * 格式化值
     * @var \Closure
     */
    protected $hookFormatValue;
    /**
     * 将DB中的值反序列化
     * @var \Closure
     */
    protected $hookValueUnserialize;
    /**
     * 将值序列化存储在DB中
     * @var \Closure
     */
    protected $hookValueSerialize;
    /**
     * 保存已完成
     * @var \Closure
     */
    protected $hookValueSaved;
    protected $hookRendering;
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
     * @var string null|left|right
     */
    protected $gridFixed = null;
    /**
     * @var bool 行内编辑
     */
    protected $gridEditable = false;

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
        FieldManager::uses(static::class);
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
     * 数据从存储系统（如数据库）读取出来，会调用此方法转换为字段 $value 值
     * @param mixed $value 字段值
     * @param AbstractField $field 字段对象
     * @return mixed
     */
    public function unserializeValue($value, AbstractField $field)
    {
        return $value;
    }

    /**
     * 字段值序列化
     * 数据提交到存储系统（如数据库）时，会调用此方法转换为存储值
     * @param mixed $value 字段值
     * @param mixed $model 当前记录
     * @return mixed
     */
    public function serializeValue($value, $model)
    {
        return $value;
    }

    /**
     * 字段输入获取准备
     * 数据从用户输入获后需要进行的处理，如转换、验证等，如果异常可以直接抛出 BizException 异常
     * @param mixed $value 字段值
     * @param array $dataSubmitted 用户提交的所有数据
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
                if (ModelUtil::hasAtttibute($item, $this->column)) {
                    $value = $item->{$this->column};
                } else {
                    $value = null;
                    //echo json_encode($this->column) . " - " . json_encode($item) . " - " . json_encode($value) . "\n";
                }
            }
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

    public function setVariable($key, $value)
    {
        $this->variables[$key] = $value;
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
        $variables = array_merge($this->fluentAttributeVariables(), $this->variables, [
            'id' => $this->id,
            'name' => $this->name(),
            'value' => $this->value(),
            'fixedValue' => $this->fixedValue(),
            'label' => $this->label,
            'column' => $this->column,
            'placeholder' => $this->placeholder(),
            'rules' => $this->rules,
        ]);
        // echo json_encode($variables, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
        return $variables;
    }

    private function transformVariables($variables, $param)
    {
        switch ($this->renderMode()) {
            case FieldRenderMode::GRID:
                if ($variables['gridEditable'] instanceof \Closure) {
                    $variables['gridEditable'] = call_user_func_array($variables['gridEditable'], [
                        $this,
                        $param['item'],
                        $param['index'],
                    ]);
                }
                break;
        }
        return $variables;
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
            $variables = $this->variables();
            if ($this->hookRendering instanceof \Closure) {
                $ret = call_user_func($this->hookRendering, $this, $item, $index);
                if (null !== $ret) {
                    if ($ret instanceof AutoRenderedFieldValue) {
                        $autoRenderedVariables = $variables;
                        $autoRenderedVariables['value'] = $ret->getValue();
                        return view('modstart::core.field.autoRenderedField-' . $this->renderMode, $autoRenderedVariables)
                            ->render();
                    }
                    return $ret;
                }
            }
            ModStart::script($this->script);
            switch ($this->renderMode) {
                case FieldRenderMode::FORM:
                    return View::make($this->view(), $variables)->render();
                case FieldRenderMode::DETAIL:
                    if (view()->exists($view = $this->view($this->renderMode))) {
                        return View::make($view, $variables)->render();
                    }
                    return View::make($this->view($this->renderMode, 'text'), $variables)->render();
                case FieldRenderMode::GRID:
                    if (view()->exists($view = $this->view($this->renderMode))) {
                        // echo json_encode($this->variables())."\n";
                        $variables = $this->transformVariables($variables, [
                            'item' => $item,
                            'index' => $index,
                        ]);
                        return View::make($view, array_merge([
                            'item' => $item,
                            '_index' => $index,
                        ], $variables))->render();
                    }
                    if (ModelUtil::hasAtttibute($item, $column)) {
                        if (is_array($item->{$column})) {
                            return htmlspecialchars(join(', ', $item->{$column}));
                        }
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
            Log::error('FieldRenderError - ' . $e->getMessage() . ' - ' . $e->getTraceAsString());
            return htmlspecialchars('FieldRenderError - ' . $e->getMessage());
        }
        throw new \Exception('FieldRenderNotExist');
    }

    public function __call($method, $arguments)
    {
        if ($this->isFluentAttribute($method)) {
            return $this->fluentAttribute($method, $arguments);
        }
        throw new \Exception('AbstractField __call error : ' . $method);
    }

}
