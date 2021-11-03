<?php


namespace Module\CmsWriter\Web\Controller;


use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\PageHtmlUtil;
use ModStart\Module\ModuleBaseController;

class ChannelController extends ModuleBaseController
{
    
    private $api;

    
    public function __construct(\Module\CmsWriter\Api\Controller\ChannelController $api)
    {
        $this->api = $api;
    }


    public function index($alias)
    {
        InputPackage::mergeToInput('channelAlias', $alias);
        $viewData = Response::tryGetData($this->api->paginate());
        $viewData['pageHtml'] = PageHtmlUtil::render($viewData['total'], $viewData['pageSize'], $viewData['page'], '?' . Request::mergeQueries(['page' => ['{page}']]));
        return $this->view('cms.channel.index', $viewData);
    }
}
