<?php


namespace Module\CmsWriter\Web\Controller;

use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\PageHtmlUtil;
use ModStart\Module\ModuleBaseController;
use Module\Member\Support\MemberLoginCheck;

class SearchController extends ModuleBaseController implements MemberLoginCheck
{
    
    private $api;

    
    public function __construct(\Module\CmsWriter\Api\Controller\SearchController $api)
    {
        $this->api = $api;
    }


    public function index()
    {
        return Response::redirect(modstart_web_url('search/notes', ['keywords' => InputPackage::buildFromInput()->getTrimString('keywords')]));
    }

    public function notes()
    {
        $input = InputPackage::buildFromInput();
        $viewData = [];
        $ret = Response::tryGetData($this->api->notes());
        $viewData['pageHtml'] = PageHtmlUtil::render($ret['total'], $ret['pageSize'], $ret['page'], '?' . Request::mergeQueries(['page' => ['{page}']]));
        $viewData['notes'] = $ret['records'];
        $viewData['keywords'] = $input->getTrimString('keywords');
        $viewData['total'] = $ret['total'];
        return $this->view('search.notes', $viewData);
    }

    public function topics()
    {
        $input = InputPackage::buildFromInput();
        $viewData = [];
        $ret = Response::tryGetData($this->api->topics());
        $viewData['pageHtml'] = PageHtmlUtil::render($ret['total'], $ret['pageSize'], $ret['page'], '?' . Request::mergeQueries(['page' => ['{page}']]));
        $viewData['topics'] = $ret['records'];
        $viewData['keywords'] = $input->getTrimString('keywords');
        $viewData['total'] = $ret['total'];
        return $this->view('search.topics', $viewData);
    }

    public function users()
    {
        $input = InputPackage::buildFromInput();
        $viewData = [];
        $ret = Response::tryGetData($this->api->users());
        $viewData['pageHtml'] = PageHtmlUtil::render($ret['total'], $ret['pageSize'], $ret['page'], '?' . Request::mergeQueries(['page' => ['{page}']]));
         $viewData['users'] = $ret['records'];
        $viewData['keywords'] = $input->getTrimString('keywords');
        $viewData['total'] = $ret['total'];
        return $this->view('search.users', $viewData);
    }
}
