<?php


namespace Module\Vendor\Provider\ContentVerify;


use ModStart\Core\Dao\ModelUtil;
use ModStart\Form\Form;
use Module\Vendor\Provider\Notifier\NotifierProvider;

abstract class AbstractContentVerifyProvider
{
    abstract public function name();

    abstract public function title();

    abstract public function config($param);

    abstract public function buildForm(Form $form, $record);

    protected function createConfig($table, $param)
    {
        return [
            'table' => $table,
        ];
    }

    public function notify($param, $title = null, $body = null)
    {
        if (null === $body) {
            $body = [
                '标题' => $title,
            ];
        }
        NotifierProvider::notifyNoneLoginOperateProcessUrl(
            $this->name(), '[审核]' . $this->title() . ($title ? '(' . $title . ')' : ''), $body, 'content_verify/' . $this->name(), $param
        );
    }

    public function record($param)
    {
        $config = $this->config($param);
        return ModelUtil::get($config['table'], intval($param['id']));
    }
}