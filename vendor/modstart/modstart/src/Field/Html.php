<?php


namespace ModStart\Field;


use ModStart\Core\Util\ConvertUtil;
use Module\Vendor\Markdown\MarkdownUtil;

class Html extends AbstractField
{
    protected $html = '';
    protected $plain = false;
    protected $isLayoutField = true;

    public function html($html)
    {
        $this->html = $html;
        return $this;
    }

    public function htmlContent($html)
    {
        $this->html = '<div class="ub-html">' . $html . '</div>';
        return $this;
    }

    public function htmlContentFromMarkdown($markdown)
    {
        $html = MarkdownUtil::convertToHtml($markdown);
        $this->html = '<div class="ub-html">' . $html . '</div>';
        return $this;
    }

    public function plain()
    {
        $this->plain = true;
        return $this;
    }

    public function render()
    {
        if ($this->html instanceof \Closure) {
            $this->html = ConvertUtil::render(
                $this->html->call($this->variables(), $this->context)
            );
        }
        if ($this->plain) {
            return $this->html;
        }
        $rules = $this->rules();
        $requiredRuleHtml = in_array('required', $rules) ? '<span class="ub-text-danger ub-text-bold">*</span>' : '';
        $label = $this->label ? $this->label . ':' : '';
        return <<<EOT
<div class="line">
    <div class="label">
        {$requiredRuleHtml}
        {$label}
    </div>
    <div class="field">
        $this->html
    </div>
</div>
EOT;
    }

}
