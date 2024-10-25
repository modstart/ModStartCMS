<?php

namespace ModStart\Core\Util;

use Illuminate\Support\Str;
use ModStart\Core\Exception\BizException;

class ExceptionUtil
{
    public static function throwExcpectException(\Exception $e, $option = [])
    {
        $option = array_merge([
            'mysql' => true,
        ], $option);
        $message = $e->getMessage();
        if (!empty($option['mysql'])) {
            if (Str::contains($message, 'Duplicate entry')) {
                BizException::throws(L('Records Duplicated'));
            }
            $formatErrorTemplates = [
                ['Data too long for column', '/Data too long for column \'(.*)\' at row/', 'FieldTooLong'],
                ['Data truncated for column', '/Data truncated for column \'(.*)\' at row/', 'FieldTooLong'],
                ['Incorrect integer value', '/ for column \'(.*)\' at row/', 'FieldFormatError'],
                ['Incorrect decimal value', '/ for column \'(.*)\' at row/', 'FieldFormatError'],
                ['Incorrect datetime value', '/ for column \'(.*)\' at row/', 'FieldFormatError'],
                ['Incorrect time value', '/ for column \'(.*)\' at row/', 'FieldFormatError'],
                ['Incorrect date value', '/ for column \'(.*)\' at row/', 'FieldFormatError'],
            ];
            $langTrans = [
                'FieldTooLong' => "Field %s Too Long",
                'FieldFormatError' => "Field %s Format Error",
            ];
            foreach ($formatErrorTemplates as $f) {
                if (Str::contains($message, $f[0])) {
                    $column = ReUtil::group1($f[1], $message);
                    if (!empty($column)) {
                        $msg = $f[2];
                        if (isset($langTrans[$msg])) {
                            $msg = L($langTrans[$msg], $column);
                        } else {
                            $msg = "$msg:$column";
                        }
                        BizException::throws($msg);
                    }
                    throw $e;
                }
            }
        }
        throw $e;
    }
}
