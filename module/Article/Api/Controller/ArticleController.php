<?php


namespace Module\Article\Api\Controller;

use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use ModStart\Module\ModuleBaseController;
use Module\Article\Util\ArticleUtil;

/**
 * Class ArticleController
 * @package Module\Article\Api\Controller
 * @Api 通用文章
 */
class ArticleController extends ModuleBaseController
{
    /**
     * @return array
     * @Api 获取通用文章
     * @ApiBodyParam id int 文章ID
     */
    public function get()
    {
        $input = InputPackage::buildFromInput();
        $id = $input->getTrimString('id');
        if (is_numeric($id)) {
            $article = ArticleUtil::get($id);
        } else {
            $article = ArticleUtil::getByAlias($id);
        }
        return Response::generateSuccessData([
            'article' => $article,
        ]);
    }
}
