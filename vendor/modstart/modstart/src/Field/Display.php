<?php


namespace ModStart\Field;


use ModStart\Core\Dao\ModelUtil;

class Display extends AbstractField
{
    protected $addable = false;
    protected $editable = false;

    public function content($content)
    {
        $this->hookRendering(function (AbstractField $field, $item, $index) use ($content) {
            return AutoRenderedFieldValue::make($content);
        });
        $this->addable(true);
        return $this;
    }

    public function asLink($linkTemplate, $openInBlank = true)
    {
        $this->hookRendering(function (AbstractField $field, $item, $index) use ($linkTemplate, $openInBlank) {
            if (preg_match_all('/\\{(.+?)\\}/', $linkTemplate, $mat)) {
                foreach ($mat[1] as $i => $k) {
                    $linkValue = ModelUtil::traverse($item, $k);
                    $linkTemplate = str_replace($mat[0][$i], $linkValue, $linkTemplate);
                }
            }
            $linkTitle = ModelUtil::traverse($item, $field->column());
            $html = [
                '<a href="', $linkTemplate, '" target="_blank" ref="noreferrer noopener" ',
                ($openInBlank ? 'target="_blank"' : ''),
                '>',
                ($openInBlank ? '<i class="iconfont icon-link"></i> ' : ''), $linkTitle,
                htmlspecialchars($linkTitle),
                '</a>',
            ];
            return AutoRenderedFieldValue::make(join('', $html));
        });
        return $this;
    }
}
