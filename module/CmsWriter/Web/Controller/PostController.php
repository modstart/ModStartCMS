<?php


namespace Module\CmsWriter\Web\Controller;


use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use ModStart\Module\ModuleBaseController;
use Module\Member\Support\MemberLoginCheck;

class PostController extends ModuleBaseController implements MemberLoginCheck
{
    public static $memberLoginCheckIgnores = ['index', 'show'];

    
    private $api;

    public function __construct(\Module\CmsWriter\Api\Controller\PostController $api)
    {
        $this->api = $api;
    }

    public function show($alias)
    {
        InputPackage::mergeToInput('alias', $alias);
        $ret = Response::tryGetData($this->api->get());
        return $this->view('cms.post.show', [
            'post' => $ret['post'],
        ]);
    }

}
