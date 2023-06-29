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

    public function asLink($linkTemplate = null, $openInBlank = true)
    {
        $this->hookRendering(function (AbstractField $field, $item, $index) use ($linkTemplate, $openInBlank) {
            if (null !== $linkTemplate) {
                $linkUrl = $linkTemplate;
                if (preg_match_all('/\\{(.+?)\\}/', $linkUrl, $mat)) {
                    foreach ($mat[1] as $i => $k) {
                        $v = ModelUtil::traverse($item, $k);
                        $linkUrl = str_replace($mat[0][$i], $v, $linkUrl);
                    }
                }
                $linkTitle = ModelUtil::traverse($item, $field->column());
            } else {
                $linkUrl = ModelUtil::traverse($item, $field->column());
                $linkTitle = $linkUrl;
            }
            if (!empty($linkTitle)) {
                $html = [
                    '<a href="', $linkUrl, '" target="_blank" ref="noreferrer noopener" ',
                    ($openInBlank ? 'target="_blank"' : ''),
                    '>',
                    ($openInBlank ? '<i class="iconfont icon-link"></i> ' : ''),
                    htmlspecialchars($linkTitle),
                    '</a>',
                ];
            } else {
                $html = [];
            }
            return AutoRenderedFieldValue::make(join('', $html));
        });
        return $this;
    }
}
