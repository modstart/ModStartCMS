<?php


namespace Module\TagManager\Api\Controller;

use Illuminate\Routing\Controller;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use Module\TagManager\Biz\TagManagerBiz;
use Module\TagManager\Model\TagManager;

class TagManagerController extends Controller
{
    public function parse()
    {
        $input = InputPackage::buildFromInput();
        $biz = TagManagerBiz::get($input->getTrimString('biz'));
        BizException::throwsIfEmpty('业务不存在', $biz);
        $content = $input->getTrimString('content');
        $tags = [];
        if (!empty($content)) {
            $tagsValue = TagManager::allTagsValue($biz->name());
            foreach ($tagsValue as $value) {
                if (str_contains($content, $value)) {
                    $tags[] = $value;
                }
            }
        }
        return Response::generateSuccessData([
            'tags' => $tags,
        ]);
    }
}
