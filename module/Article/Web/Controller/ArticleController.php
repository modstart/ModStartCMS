<?php


namespace Module\Article\Web\Controller;


use ModStart\Core\Input\Response;
use ModStart\Core\Util\ArrayUtil;
use ModStart\Module\ModuleBaseController;
use Module\Article\Util\ArticleUtil;

class ArticleController extends ModuleBaseController
{
    public function views($id)
    {
        if (is_numeric($id)) {
            $article = ArticleUtil::get($id);
        } else {
            $article = ArticleUtil::getByAlias($id);
        }
        if (empty($article)) {
            return Response::page404();
        }
        $view = 'article.view';
        if ($article['position'] == 'page' || empty($article['position'])) {
            $view = 'article.viewPage';
        }
        return $this->view($view, [
            'article' => $article
        ]);
    }
}
