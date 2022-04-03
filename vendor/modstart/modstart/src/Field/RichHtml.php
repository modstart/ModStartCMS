<?php


namespace ModStart\Field;


use ModStart\Core\Util\HtmlUtil;

class RichHtml extends AbstractField
{
    protected $listable = false;
    protected static $js = [
        'asset/common/editor.js',
    ];

    protected function setup()
    {
        $this->addVariables([
            'server' => modstart_admin_url('data/ueditor'),
        ]);
    }

    public function server($server)
    {
        $this->addVariables(['server' => $server]);
        return $this;
    }

    public function prepareInput($value, $dataSubmitted)
    {
        return HtmlUtil::filter($value);
    }


}
