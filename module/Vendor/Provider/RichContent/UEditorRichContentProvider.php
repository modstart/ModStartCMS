<?php


namespace Module\Vendor\Provider\RichContent;


use Illuminate\Support\Facades\View;
use ModStart\Core\Util\HtmlUtil;
use Module\Vendor\Html\HtmlConvertUtil;

class UEditorRichContentProvider extends AbstractRichContentProvider
{
    const NAME = 'htmlUEditor';

    public function name()
    {
        return self::NAME;
    }

    public function title()
    {
        return 'UEditor富文本';
    }

    public function render($name, $value, $param = [])
    {
        return View::make('module::Vendor.View.widget.richContent.htmlUeditor', [
            'name' => $name,
            'value' => $value,
            'param' => $param,
        ])->render();
    }

    public function toHtml($value, $htmlInterceptors = null)
    {
        $value = HtmlUtil::filter($value);
        return parent::toHtml($value, $htmlInterceptors);
    }


}
