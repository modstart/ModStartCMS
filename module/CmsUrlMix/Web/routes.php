<?php
/* @var \Illuminate\Routing\Router $router */

use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
use Module\Cms\Util\CmsRouteUtil;

$router->group([
    'middleware' => [
        \Module\Member\Middleware\WebAuthMiddleware::class,
    ],
], function () use ($router) {

    foreach (\Module\Cms\Util\CmsCatUtil::allSafely() as $item) {
        if (!empty($item['fullUrl'])) {
            $router->get($item['fullUrl'], function () use ($item) {
                InputPackage::mergeToInput('page', 1);
                return CmsRouteUtil::rewrite('c/' . $item['id']);
            });
        }
        if (!empty($item['pageFullUrl'])) {
            $pageUrl = $item['pageFullUrl'];
            $pageKey = null;
            if (str_contains($item['pageFullUrl'], '?')) {
                list($pageUrl, $paramUrl) = explode('?', $item['pageFullUrl']);
                if (preg_match('/([a-zA-Z0-9]*?)=\\{page\\}/', $paramUrl, $mat)) {
                    $pageKey = $mat[1];
                }
            }
            $router->get($pageUrl, function ($page = 1) use ($item, $pageKey) {
                $param = [];
                if (!empty($pageKey)) {
                    $input = InputPackage::buildFromInput();
                    $page = $input->getInteger($pageKey);
                }
                $param['page'] = $page;
                return CmsRouteUtil::rewrite('c/' . $item['id'], $param);
            });
        }
    }

    $urlContent = modstart_config('CmsUrlMix_ContentUrlRoutes', '');
    if (!empty($urlContent)) {
        $urlContent = explode("\n", $urlContent);
        foreach ($urlContent as $item) {
            $item = trim($item);
            if (!empty($item)) {
                $router->get($item, function () {
                    $path = Request::path();
                    $content = ModelUtil::get('cms_content', ['fullUrl' => $path]);
                    if (!empty($content)) {
                        return CmsRouteUtil::rewrite('a/' . $content['id']);
                    }
                    return Response::page404();
                });
            }
        }
    }

});




