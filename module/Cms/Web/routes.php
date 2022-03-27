<?php
/* @var \Illuminate\Routing\Router $router */

use Module\Cms\Type\CmsMode;
use Module\Cms\Util\CmsCatUtil;

$router->group([
    'middleware' => [
        \Module\Member\Middleware\WebAuthMiddleware::class,
    ],
], function () use ($router) {

    $router->match(['get', 'post'], 'cms', 'IndexController@index');
    $router->match(['get', 'post'], 'tag/{tag}', 'TagController@index');
    $router->match(['get', 'post'], 'a/{alias_url}', 'DetailController@index');
    $router->match(['get', 'post'], 'c/{id}', 'ListController@index');

    $router->match(['get', 'post'], 'search', 'SearchController@index');

    $cats = CmsCatUtil::allSafelyHavingUrl();
    foreach ($cats as $item) {
        switch ($item['_model']['mode']) {
            case CmsMode::LIST_DETAIL:
                $router->match(['get'], $item['url'], 'ListController@index');
                break;
            case CmsMode::FORM:
                $router->match(['get'], $item['url'], 'FormController@index');
                $router->match(['post'], $item['url'], 'FormController@submit');
                break;
            case CmsMode::PAGE:
                $router->match(['get'], $item['url'], 'PageController@index');
                break;
        }
    }
    if (modstart_config('Cms_ContentUrlMode') == \Module\Cms\Type\ContentUrlMode::CAT) {
        foreach ($cats as $item) {
            $router->match(['get'], $item['url'] . '/{alias_url}', 'DetailController@index');
        }
    }

});

