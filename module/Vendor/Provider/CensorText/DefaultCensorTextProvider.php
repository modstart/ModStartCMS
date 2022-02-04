<?php


namespace Module\Vendor\Provider\CensorText;


use ModStart\Core\Input\Response;

class DefaultCensorTextProvider extends AbstractCensorTextProvider
{
    public function name()
    {
        return 'default';
    }

    public function title()
    {
        return '无检测';
    }

    public function verify($content, $param = [])
    {
        return Response::generateSuccessData([
            'pass' => false,
        ]);
    }

}