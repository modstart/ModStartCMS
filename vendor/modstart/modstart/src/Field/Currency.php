<?php


namespace ModStart\Field;


class Currency extends AbstractField
{
    protected $view = 'modstart::core.field.number';
    protected $rules = ['regex:/^\\d+(\\.\\d+)?$/i'];
}
