<?php

namespace Module\Survey\Type;


use ModStart\Core\Type\BaseType;

class SurveyQuestionType implements BaseType
{
    const SINGLE_CHOICE = 1;
    const MULTI_CHOICE = 2;
    const TEXT = 3;
    const BIG_TEXT = 4;

    public static function getList()
    {
        return [
            self::SINGLE_CHOICE => '单选',
            self::MULTI_CHOICE => '多选',
            self::TEXT => '单行文本',
            self::BIG_TEXT => '多行文本',
        ];
    }

}
