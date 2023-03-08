<?php


namespace ModStart\Support\Manager;

use ModStart\Field\AbstractField;
use ModStart\Support\Concern\HasFields;

class FieldManager
{
    private static $availableFields = [];
    private static $collectedAssets = [];
    private static $usedFields = [];

    public static function registerBuiltinFields()
    {
        $map = [
            'text' => \ModStart\Field\Text::class,
            'display' => \ModStart\Field\Display::class,
            'tree' => \ModStart\Field\Tree::class,
            'custom' => \ModStart\Field\Custom::class,
            'checkbox' => \ModStart\Field\Checkbox::class,
            'tags' => \ModStart\Field\Tags::class,
            'code' => \ModStart\Field\Code::class,
            'type' => \ModStart\Field\Type::class,
            'password' => \ModStart\Field\Password::class,
            'image' => \ModStart\Field\Image::class,
            'images' => \ModStart\Field\Images::class,
            'imagesTemp' => \ModStart\Field\ImagesTemp::class,
            'link' => \ModStart\Field\Link::class,
            'switch' => \ModStart\Field\SwitchField::class,
            'textarea' => \ModStart\Field\Textarea::class,
            'json' => \ModStart\Field\Json::class,
            'jsonKeyValue' => \ModStart\Field\JsonKeyValue::class,
            'jsonIdItems' => \ModStart\Field\JsonIdItems::class,
            'color' => \ModStart\Field\Color::class,
            'date' => \ModStart\Field\Date::class,
            'datetime' => \ModStart\Field\Datetime::class,
            'time' => \ModStart\Field\Time::class,
            'period' => \ModStart\Field\Period::class,
            'radio' => \ModStart\Field\Radio::class,
            'select' => \ModStart\Field\Select::class,
            'selectRemote' => \ModStart\Field\SelectRemote::class,
            'richHtml' => \ModStart\Field\RichHtml::class,
            'markdown' => \ModStart\Field\Markdown::class,
            'keyValueList' => \ModStart\Field\KeyValueList::class,
            'complexFields' => \ModStart\Field\ComplexFields::class,
            'values' => \ModStart\Field\Values::class,
            'customField' => \ModStart\Field\CustomField::class,
            'html' => \ModStart\Field\Html::class,
            'cascadeGroup' => \ModStart\Field\CascadeGroup::class,
            'number' => \ModStart\Field\Number::class,
            'rate' => \ModStart\Field\Rate::class,
            'percent' => \ModStart\Field\Percent::class,
            'decimal' => \ModStart\Field\Decimal::class,
            'currency' => \ModStart\Field\Currency::class,
            'id' => \ModStart\Field\Id::class,
            'captcha' => \ModStart\Field\Captcha::class,
            'fileTemp' => \ModStart\Field\FileTemp::class,
            'file' => \ModStart\Field\File::class,
            'files' => \ModStart\Field\Files::class,
            'video' => \ModStart\Field\Video::class,
            'audio' => \ModStart\Field\Audio::class,
            'areaChina' => \ModStart\Field\AreaChina::class,
            'hidden' => \ModStart\Field\Hidden::class,
            'icon' => \ModStart\Field\Icon::class,
            'button' => \ModStart\Field\Button::class,

            'layoutGrid' => \ModStart\Layout\LayoutGrid::class,
            'layoutTab' => \ModStart\Layout\LayoutTab::class,
            'layoutPanel' => \ModStart\Layout\LayoutPanel::class,
            'layoutSeparator' => \ModStart\Layout\LayoutSeparator::class,
            'layoutHtml' => \ModStart\Layout\LayoutHtml::class,
            'layoutLine' => \ModStart\Layout\LayoutLine::class,
        ];

        foreach ($map as $abstract => $class) {
            FieldManager::extend($abstract, $class);
        }
    }

    public static function collectFieldAssets($type = 'js|css|script|style')
    {
        if (!in_array($type, ['js', 'css', 'script', 'style'])) {
            return [];
        }
        if (isset(static::$collectedAssets[$type])) {
            return static::$collectedAssets[$type];
        }
        $assets = collect();
        foreach (static::$availableFields as $name => $field) {
            if (in_array($type, ['js', 'script'])) {
                if (empty(static::$usedFields[$name])) {
                    continue;
                }
            }
            if (!method_exists($field, 'getAssets')) {
                continue;
            }
            $assets->push(array_get(call_user_func([$field, 'getAssets']), $type));
        }
        static::$collectedAssets[$type] = $assets->flatten()->unique()->filter()->toArray();
        return static::$collectedAssets[$type];
    }

    public static function extend($field, $class)
    {
        static::$availableFields[$field] = $class;
    }

    public static function findFieldClass($method)
    {
        $class = array_get(static::$availableFields, $method);
        if (class_exists($class)) {
            return $class;
        }
        return false;
    }

    /**
     * @param HasFields $context
     * @param $fieldName
     * @param $column
     * @param $arguments
     * @return AbstractField
     */
    public static function make($context, $fieldName, $column, ...$arguments)
    {
        $className = static::findFieldClass($fieldName);
        /** @var AbstractField $element */
        $element = new $className($column, $arguments);
        $element->renderMode($context->fieldDefaultRenderMode());
        $element->context($context);
        return $element;
    }

    public static function call($context, $method, $arguments)
    {
        /** @var HasFields $context */
        if ($className = static::findFieldClass($method)) {
            static::$usedFields[$method] = true;
            $column = array_get($arguments, 0, '');
            /** @var AbstractField $element */
            $element = new $className($column, array_slice($arguments, 1));
            $element->renderMode($context->fieldDefaultRenderMode());
            $element->context($context);
            $context->pushField($element);
            $element->postSetup();
            return $element;
        }
        throw new \Exception("Field [" . ucfirst($method) . "] not exists or registered, available: " . json_encode(array_keys(static::$availableFields)));
    }

}
