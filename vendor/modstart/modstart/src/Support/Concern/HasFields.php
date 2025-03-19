<?php


namespace ModStart\Support\Concern;

use Illuminate\Support\Collection;
use ModStart\Field\AbstractField;
use ModStart\Field\Type\FieldRenderMode;
use ModStart\ModStart;

/**
 * 字段管理能力
 *
 * Trait HasFields
 * @package ModStart\Support\Concern
 *
 *
 * @method \ModStart\Field\AdminUser            adminUser($column, $label = '')
 * @method \ModStart\Field\AreaChina            areaChina($column, $label = '')
 * @method \ModStart\Field\Audio                audio($column, $label = '')
 * @method \ModStart\Field\Button               button($column, $label = '')
 * @method \ModStart\Field\Captcha              captcha($column, $label = '')
 * @method \ModStart\Field\Checkbox             checkbox($column, $label = '')
 * @method \ModStart\Field\Code                 code($column, $label = '')
 * @method \ModStart\Field\Color                color($column, $label = '')
 * @method \ModStart\Field\ComplexFields        complexFields($column, $label = '')
 * @method \ModStart\Field\ComplexFieldsList    complexFieldsList($column, $label = '')
 * @method \ModStart\Field\Currency             currency($column, $label = '')
 * @method \ModStart\Field\Custom               custom($column, $label = '')
 * @method \ModStart\Field\CustomField          customField($column, $label = '')
 * @method \ModStart\Field\Date                 date($column, $label = '')
 * @method \ModStart\Field\Datetime             datetime($column, $label = '')
 * @method \ModStart\Field\Decimal              decimal($column, $label = '')
 * @method \ModStart\Field\Display              display($column, $label = '')
 * @method \ModStart\Field\DynamicFields        dynamicFields($column, $label = '')
 * @method \ModStart\Field\File                 file($column, $label = '')
 * @method \ModStart\Field\FileTemp             fileTemp($column, $label = '')
 * @method \ModStart\Field\Files                files($column, $label = '')
 * @method \ModStart\Field\Html                 html($column, $label = '')
 * @method \ModStart\Field\Hidden               hidden($column, $label = '')
 * @method \ModStart\Field\Id                   id($column, $label = '')
 * @method \ModStart\Field\Image                image($column, $label = '')
 * @method \ModStart\Field\Images               images($column, $label = '')
 * @method \ModStart\Field\ImagesTemp           imagesTemp($column, $label = '')
 * @method \ModStart\Field\Icon                 icon($column, $label = '')
 * @method \ModStart\Field\Json                 json($column, $label = '')
 * @method \ModStart\Field\JsonIdItems          jsonIdItems($column, $label = '')
 * @method \ModStart\Field\JsonKeyValue         jsonKeyValue($column, $label = '')
 * @method \ModStart\Field\KeyValueList         keyValueList($column, $label = '')
 * @method \ModStart\Field\Link                 link($column, $label = '')
 * @method \ModStart\Field\ManyRelation         manyRelation($column, $label = '')
 * @method \ModStart\Field\Markdown             markdown($column, $label = '')
 * @method \ModStart\Field\MultiSelect          multiSelect($column, $label = '')
 * @method \ModStart\Field\Number               number($column, $label = '')
 * @method \ModStart\Field\NumberRange          numberRange($column, $label = '')
 * @method \ModStart\Field\Password             password($column, $label = '')
 * @method \ModStart\Field\Percent              percent($column, $label = '')
 * @method \ModStart\Field\Period               period($column, $label = '')
 * @method \ModStart\Field\Radio                radio($column, $label = '')
 * @method \ModStart\Field\Rate                 rate($column, $label = '')
 * @method \ModStart\Field\Raw                  raw($column, $label = '')
 * @method \ModStart\Field\RichHtml             richHtml($column, $label = '')
 * @method \ModStart\Field\Select               select($column, $label = '')
 * @method \ModStart\Field\SelectRemote         selectRemote($column, $label = '')
 * @method \ModStart\Field\SwitchField          switch ($column, $label = '')
 * @method \ModStart\Field\Tags                 tags($column, $label = '')
 * @method \ModStart\Field\Textarea             textarea($column, $label = '')
 * @method \ModStart\Field\Text                 text($column, $label = '')
 * @method \ModStart\Field\Time                 time($column, $label = '')
 * @method \ModStart\Field\Transfer             transfer($column, $label = '')
 * @method \ModStart\Field\Tree                 tree($column, $label = '')
 * @method \ModStart\Field\Type                 type($column, $label = '')
 * @method \ModStart\Field\Values               values($column, $label = '')
 * @method \ModStart\Field\Video                video($column, $label = '')
 *
 * @example $callback = function (LayoutGrid $layout) { $layout->layoutColumn(4, function ($builder) { }); });
 * @method \ModStart\Layout\LayoutGrid          layoutGrid($callback)
 * @example $callback = function (LayoutTable $layout) { $layout->layoutRow( function ($layout) {  $layout->layoutCol(function ($builder) { });  }); });
 * @method \ModStart\Layout\LayoutTable         layoutTable($callback)
 * @example $callback = function (LayoutTab $layout) { $layout->tab('title',closure});
 * @method \ModStart\Layout\LayoutTab           layoutTab($callback)
 * @example $title = 'title', $callback = function (Form $form) { })
 * @method \ModStart\Layout\LayoutPanel         layoutPanel($title, $callback)
 * @example $title = 'title', $callback = function (Form $form) { })
 * @method \ModStart\Layout\LayoutLine          layoutLine($title, $callback)
 * @method \ModStart\Layout\LayoutSeparator     layoutSeparator($title)
 * @method \ModStart\Layout\LayoutHtml          layoutHtml($html)
 */
trait HasFields
{
    /**
     * 字段集合
     * @var AbstractField[]
     */
    private $fields;
    /**
     * 默认字段渲染模式 @see FieldRenderMode
     * @var string
     */
    private $fieldDefaultRenderMode = 'add';

    private function setupFields()
    {
        $this->fields = new Collection();
    }

    /**
     * 填充所有字段
     */
    public function fillFields()
    {
        $this->fields()->each(function (AbstractField $field) {
            $field->fill($this->item);
        });
    }

    /**
     * 填充所有可编辑字段
     */
    public function fillEditableFields()
    {
        $this->editableFields(true)->each(function (AbstractField $field) {
            $field->fill($this->item);
        });
    }

    /**
     * 填充所有可增加字段
     */
    public function fillAddableFields()
    {
        $this->addableFields(true)->each(function (AbstractField $field) {
            $field->fill($this->item);
        });
    }

    /**
     * 增加一个字段
     * @param AbstractField $field
     * @return $this
     */
    public function pushField(AbstractField $field)
    {
        $this->fields()->push($field);
        return $this;
    }

    /**
     * 移除一个字段
     * @param $column
     * @return $this
     */
    public function removeField($column)
    {
        $this->fields = $this->fields()->filter(function (AbstractField $field) use ($column) {
            return $field->column() != $column;
        });
        return $this;
    }

    /**
     * 在之前追加一个字段
     * @param AbstractField $field
     * @return $this
     */
    public function prependField(AbstractField $field)
    {
        $this->fields()->prepend($field);
        return $this;
    }

    public function fieldDefaultRenderMode($value = null)
    {
        if (null === $value) {
            return $this->fieldDefaultRenderMode;
        }
        return $this->fieldDefaultRenderMode = $value;
    }

    /**
     * @return Collection
     */
    public function fields()
    {
        return $this->fields;
    }

    /**
     * 获取列表中的字段
     * @return AbstractField[]
     */
    public function listableFields()
    {
        return $this->fields->filter(function (AbstractField $item) {
            return $item->listable();
        });
    }

    /**
     * 获取所有可增加字段
     * @param $includeShowOnly bool 是否包含只读字段
     * @return AbstractField[]
     */
    public function addableFields($includeShowOnly = false)
    {
        return $this->fields->filter(function (AbstractField $item) use ($includeShowOnly) {
            return $item->addable() || ($includeShowOnly && $item->formShowOnly());
        });
    }

    /**
     *  获取所有可详情显示字段
     * @return AbstractField[]
     */
    protected function editableFields($includeShowOnly = false)
    {
        return $this->fields->filter(function (AbstractField $item) use ($includeShowOnly) {
            return $item->editable() || ($includeShowOnly && $item->formShowOnly());
        });
    }

    /**
     *  获取所有可详情显示字段
     * @return AbstractField[]
     */
    protected function showableFields()
    {
        return $this->fields->filter(function (AbstractField $item) {
            return $item->showable();
        });
    }

    /**
     * 获取所有可增加字段
     * @return AbstractField[]
     */
    public function sortableFields()
    {
        return $this->fields->filter(function (AbstractField $item) {
            return $item->sortable();
        });
    }

    /**
     * 根据表单name获取字段
     * @param $name
     * @return AbstractField
     */
    public function getFieldByName($name)
    {
        if ('laravel9' == ModStart::env()) {
            return $this->fields->first(function (AbstractField $item, $k) use ($name) {
                return $item->name() == $name;
            });
        }
        return $this->fields->first(function ($k, AbstractField $item) use ($name) {
            return $item->name() == $name;
        });
    }

    /**
     * 根据字段获取字段
     * @param $name
     * @return AbstractField
     */
    public function getFieldByColumn($column)
    {
        if ('laravel9' == ModStart::env()) {
            return $this->fields->first(function (AbstractField $item, $k) use ($column) {
                return $item->column() == $column;
            });
        }
        return $this->fields->first(function ($k, AbstractField $item) use ($column) {
            return $item->column() == $column;
        });
    }

}
