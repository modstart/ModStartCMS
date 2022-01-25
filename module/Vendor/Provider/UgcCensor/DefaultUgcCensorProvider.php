<?php


namespace Module\Vendor\Provider\UgcCensor;


use ModStart\Core\Input\Response;

class DefaultUgcCensorProvider extends AbstractUgcCensorProvider
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