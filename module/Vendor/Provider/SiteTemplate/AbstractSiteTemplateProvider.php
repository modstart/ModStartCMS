<?php


namespace Module\Vendor\Provider\SiteTemplate;


use ModStart\Form\Form;

/**
 * Class AbstractSiteTemplateProvider
 * @package Module\Vendor\Provider\SiteTemplate
 * @since 1.5.0
 */
abstract class AbstractSiteTemplateProvider
{
    abstract public function name();

    abstract public function title();

    public function root()
    {
        return null;
    }

    /**
     * 主题是否有额外定制配置
     * @return false
     */
    public function hasConfig()
    {
        return false;
    }

    /**
     * 主题的配置表单
     * @param $form Form 表单
     * @param $param array 额外参数
     */
    public function config(Form $form, $param = [])
    {

    }
}
