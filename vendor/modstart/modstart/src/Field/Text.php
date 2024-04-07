<?php


namespace ModStart\Field;


use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Util\StrUtil;
use ModStart\Field\Type\FieldRenderMode;

class Text extends AbstractField
{

    protected function setup()
    {
        $this->addVariables([
            'autoTrim' => false,
        ]);
    }

    /**
     * 保存时自动清除空格
     * @param bool $enable
     * @return $this
     */
    public function autoTrim($enable = true)
    {
        $this->addVariables(['autoTrim' => $enable]);
        return $this;
    }

    public function prepareInput($value, $dataSubmitted)
    {
        if ($this->variables['autoTrim']) {
            $value = StrUtil::filterSpecialChars($value);
            $value = trim($value);
        }
        return $value;
    }

    public function asLink($linkTemplate = null, $openInBlank = true)
    {
        $this->hookRendering(function (AbstractField $field, $item, $index) use ($linkTemplate, $openInBlank) {
            switch ($field->renderMode()) {
                case FieldRenderMode::DETAIL:
                case FieldRenderMode::GRID:
                    if (null !== $linkTemplate) {
                        if ($linkTemplate instanceof \Closure || strpos($linkTemplate, '::') !== false) {
                            $linkUrl = call_user_func($linkTemplate, $item);
                        } else {
                            $linkUrl = $linkTemplate;
                            if (preg_match_all('/\\{(.+?)\\}/', $linkUrl, $mat)) {
                                foreach ($mat[1] as $i => $k) {
                                    $v = ModelUtil::traverse($item, $k);
                                    $linkUrl = str_replace($mat[0][$i], $v, $linkUrl);
                                }
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
            }
        });
        return $this;
    }
}
