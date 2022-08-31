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
            'editorMode' => 'default',
            'server' => modstart_admin_url('data/ueditor'),
            'htmlFilter' => true,
        ]);
    }

    /**
     * 编辑器模式
     * @param $mode string default|simple
     * @return $this
     */
    public function editorMode($mode)
    {
        $this->addVariables(['editorMode' => $mode]);
        return $this;
    }

    public function htmlFilter($filter = true)
    {
        $this->addVariables(['htmlFilter' => $filter]);
        return $this;
    }

    public function server($server)
    {
        $this->addVariables(['server' => $server]);
        return $this;
    }

    public function prepareInput($value, $dataSubmitted)
    {
        if ($this->variables['htmlFilter']) {
            $value = HtmlUtil::filter($value);
        }
        return $value;
    }


}
