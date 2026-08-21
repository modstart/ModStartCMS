<?php


namespace Module\Article\Api\Controller;

use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use ModStart\Module\ModuleBaseController;
use Module\Article\Util\ArticleUtil;

/**
 * Class ArticleController
 * @package Module\Article\Api\Controller
 * @Api 物料
 */
class ArticleController extends ModuleBaseController
{
    /**
     * @Api 获取通用文章
     * @ApiDesc 根据 ID 或别名获取文章内容
     * @ApiMethod post
     * @ApiBodyParam id string 文章ID或别名
     * @ApiResponseData {
     *   "article": {
     *     "id": 1,
     *     "title": "标题",
     *     "content": "内容"
     *   }
     * }
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
