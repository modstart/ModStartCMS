<?php


namespace Module\Cms\Field;


use ModStart\Core\Exception\BizException;

class CmsField
{
    /**
     * @var array
     */
    private static $list = [
        TextCmsField::class,
        TextareaCmsField::class,
        RadioCmsField::class,
        SelectCmsField::class,
        CheckboxCmsField::class,
        ImageCmsField::class,
        ImagesCmsField::class,
        FileCmsField::class,
        DateCmsField::class,
        DatetimeCmsField::class,
        RichTextCmsField::class,
        VideoCmsField::class,
        AudioCmsField::class,
    ];

    public static function register($field)
    {
        self::$list[] = $field;
    }

    public static function registerAll(...$fields)
    {
        foreach ($fields as $field) {
            self::register($field);
        }
    }

    /**
     * @return AbstractCmsField[]
     */
    public static function all()
    {
        foreach (self::$list as $k => $v) {
            if ($v instanceof \Closure) {
                self::$list[$k] = call_user_func($v);
            } else if (is_string($v)) {
                self::$list[$k] = app($v);
            }
        }
        return self::$list;
    }

    /**
     * @param $name
     * @return AbstractCmsField|null
     */
    public static function getByName($name)
    {
        foreach (self::all() as $item) {
            if ($item->name() == $name) {
                return $item;
            }
        }
        return null;
    }

    public static function getByNameOrFail($name)
    {
        $item = self::getByName($name);
        BizException::throwsIfEmpty('CmsField not found', $item);
        return $item;
    }

    public static function allMap()
    {
        return array_build(self::all(), function ($k, $v) {
            return [
                $v->name(), $v->title()
            ];
        });
    }
}
