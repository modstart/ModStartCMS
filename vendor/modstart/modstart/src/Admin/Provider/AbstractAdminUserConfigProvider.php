<?php


namespace ModStart\Admin\Provider;


abstract class AbstractAdminUserConfigProvider
{
    public abstract function name();

    public abstract function title();

    public abstract function renderForm($item, $param = []);

    public abstract function renderDetail($item, $param = []);

    public function renderGrid($item, $param = [])
    {
        return $this->renderDetail($item, $param);
    }

    public abstract function saved($item, $param = []);

    public abstract function deleted($item, $param = []);
}
