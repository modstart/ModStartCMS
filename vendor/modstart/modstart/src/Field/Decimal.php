<?php


namespace ModStart\Field;


class Decimal extends AbstractField
{
    protected $view = 'modstart::core.field.number';
    protected $rules = ['regex:/^-?\\d+(\\.\\d+)?$/i'];
}
