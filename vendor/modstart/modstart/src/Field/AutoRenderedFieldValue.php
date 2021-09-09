<?php


namespace ModStart\Field;


use Illuminate\Support\Facades\View;

class AutoRenderedFieldValue
{
    private $value;

    
    public function __construct($value)
    {
        $this->value = $value;
    }

    public static function make($value)
    {
        return new static($value);
    }

    public static function makeView($view, $viewData = [])
    {
        return self::make(View::make($view, $viewData)->render());
    }

    
    public function getValue()
    {
        return $this->value;
    }

}