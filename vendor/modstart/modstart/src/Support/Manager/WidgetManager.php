<?php


namespace ModStart\Support\Manager;

use ModStart\Widget\AbstractWidget;

class WidgetManager
{
    private static $availableWidgets = [];
    private static $collectedAssets = null;
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

    public static function registerBuiltinWidgets()
    {
        $map = [
            'statusText' => \ModStart\Widget\StatusText::class,
            'textAjaxRequest' => \ModStart\Widget\TextAjaxRequest::class,
            'textDialogRequest' => \ModStart\Widget\TextDialogRequest::class,
            'textAction' => \ModStart\Widget\TextAction::class,
            'textLink' => \ModStart\Widget\TextLink::class,
            'buttonAjaxRequest' => \ModStart\Widget\ButtonAjaxRequest::class,
            'buttonDialogRequest' => \ModStart\Widget\ButtonDialogRequest::class,
        ];
        foreach ($map as $abstract => $class) {
            WidgetManager::extend($abstract, $class);
        }
    }

    public static function collectWidgetAssets($type = 'js|css|script|style')
    {
        if (!in_array($type, ['js', 'css', 'script', 'style'])) {
            return [];
        }
        if (isset(static::$collectedAssets[$type])) {
            return static::$collectedAssets[$type];
        }
        $assets = collect();
        foreach (static::$availableWidgets as $name => $field) {
            if (empty(self::$uses[$field])) {
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
        static::$availableWidgets[$field] = $class;
    }

    public static function findWidgetClass($method)
    {
        $class = array_get(static::$availableWidgets, $method);
        if (class_exists($class)) {
            return $class;
        }
        return false;
    }

    public static function call($context, $method, $arguments)
    {
        if ($className = static::findWidgetClass($method)) {
            $column = array_get($arguments, 0, '');
            $element = new $className($column, array_slice($arguments, 1));
            $context->pushField($element);
            return $element;
        }
        throw new \Exception("Widget [" . ucfirst($method) . "] not exists or registered, available: " . json_encode(array_keys(static::$availableWidgets)));
    }

}
