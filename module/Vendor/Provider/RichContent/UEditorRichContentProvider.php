<?php


namespace Module\Vendor\Provider\RichContent;


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

}
