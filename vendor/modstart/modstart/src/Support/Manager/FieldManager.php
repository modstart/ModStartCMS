<?php


namespace ModStart\Support\Manager;

use ModStart\Field\AbstractField;
use ModStart\Support\Concern\HasFields;

class FieldManager
{
    private static $availableFields = [];
    private static $collectedAssets = [];
    private static $uses = [];

    public static function uses($cls)
    {
        if (is_array($cls)) {
            foreach ($cls as $c) {
                self::$uses[$c] = true;
            }
        } else {
            self::$uses[$cls] = true;
        }
    }

    public static function registerBuiltinFields()
    {
        $map = [
            'adminUser' => \ModStart\Field\AdminUser::class,
            'areaChina' => \ModStart\Field\AreaChina::class,
            'audio' => \ModStart\Field\Audio::class,
            'button' => \ModStart\Field\Button::class,
            'captcha' => \ModStart\Field\Captcha::class,
            'cascadeGroup' => \ModStart\Field\CascadeGroup::class,
            'checkbox' => \ModStart\Field\Checkbox::class,
            'code' => \ModStart\Field\Code::class,
            'color' => \ModStart\Field\Color::class,
            'complexFields' => \ModStart\Field\ComplexFields::class,
            'complexFieldsList' => \ModStart\Field\ComplexFieldsList::class,
            'currency' => \ModStart\Field\Currency::class,
            'custom' => \ModStart\Field\Custom::class,
            'customField' => \ModStart\Field\CustomField::class,
            'date' => \ModStart\Field\Date::class,
            'datetime' => \ModStart\Field\Datetime::class,
            'decimal' => \ModStart\Field\Decimal::class,
            'display' => \ModStart\Field\Display::class,
            'dynamicFields' => \ModStart\Field\DynamicFields::class,
            'file' => \ModStart\Field\File::class,
            'fileTemp' => \ModStart\Field\FileTemp::class,
            'files' => \ModStart\Field\Files::class,
            'hidden' => \ModStart\Field\Hidden::class,
            'html' => \ModStart\Field\Html::class,
            'icon' => \ModStart\Field\Icon::class,
            'id' => \ModStart\Field\Id::class,
            'image' => \ModStart\Field\Image::class,
            'images' => \ModStart\Field\Images::class,
            'imagesTemp' => \ModStart\Field\ImagesTemp::class,
            'json' => \ModStart\Field\Json::class,
            'jsonIdItems' => \ModStart\Field\JsonIdItems::class,
            'jsonKeyValue' => \ModStart\Field\JsonKeyValue::class,
            'keyValueList' => \ModStart\Field\KeyValueList::class,
            'link' => \ModStart\Field\Link::class,
            'manyRelation' => \ModStart\Field\ManyRelation::class,
            'markdown' => \ModStart\Field\Markdown::class,
            'number' => \ModStart\Field\Number::class,
            'password' => \ModStart\Field\Password::class,
            'percent' => \ModStart\Field\Percent::class,
            'period' => \ModStart\Field\Period::class,
            'radio' => \ModStart\Field\Radio::class,
            'rate' => \ModStart\Field\Rate::class,
            'richHtml' => \ModStart\Field\RichHtml::class,
            'select' => \ModStart\Field\Select::class,
            'selectRemote' => \ModStart\Field\SelectRemote::class,
            'switch' => \ModStart\Field\SwitchField::class,
            'tags' => \ModStart\Field\Tags::class,
            'text' => \ModStart\Field\Text::class,
            'textarea' => \ModStart\Field\Textarea::class,
            'time' => \ModStart\Field\Time::class,
            'tree' => \ModStart\Field\Tree::class,
            'type' => \ModStart\Field\Type::class,
            'values' => \ModStart\Field\Values::class,
            'video' => \ModStart\Field\Video::class,

            'layoutGrid' => \ModStart\Layout\LayoutGrid::class,
            'layoutTable' => \ModStart\Layout\LayoutTable::class,
            'layoutTab' => \ModStart\Layout\LayoutTab::class,
            'layoutPanel' => \ModStart\Layout\LayoutPanel::class,
            'layoutLine' => \ModStart\Layout\LayoutLine::class,
            'layoutSeparator' => \ModStart\Layout\LayoutSeparator::class,
            'layoutHtml' => \ModStart\Layout\LayoutHtml::class,
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
            if (!isset(self::$uses[$field])) {
                continue;
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
