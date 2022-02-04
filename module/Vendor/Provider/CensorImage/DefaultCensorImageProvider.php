<?php


namespace Module\Vendor\Provider\CensorImage;


use ModStart\Core\Input\Response;

class DefaultCensorImageProvider extends AbstractCensorImageProvider
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