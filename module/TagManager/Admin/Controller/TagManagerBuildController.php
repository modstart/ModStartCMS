<?php


namespace Module\TagManager\Admin\Controller;


use Illuminate\Routing\Controller;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
use Module\TagManager\Biz\TagManagerBiz;
use Module\TagManager\Model\TagManager;

class TagManagerBuildController extends Controller
{
    public function index()
    {
        if (Request::isPost()) {
            $input = InputPackage::buildFromInput();
            $biz = $input->getTrimString('biz');
            $action = $input->getTrimString('action');
            $biz = TagManagerBiz::get($biz);
            BizException::throwsIfEmpty('Biz错误', $biz);
            switch ($action) {
                case 'refresh':
                    return Response::generateSuccessData([
                        'count' => ModelUtil::count('tag_manager', ['biz' => $biz->name()]),
                    ]);
                case 'sync':
                    $nextId = $input->getInteger('nextId', 0);
                    if ($nextId <= 0) {
                        ModelUtil::delete('tag_manager', ['biz' => $biz->name()]);
                    }
                    $batch = $biz->syncBatch($nextId);
                    TagManager::putTags($biz->name(), $batch['tags']);
                    return Response::generateSuccessData([
                        'finish' => $batch['finish'],
                        'nextId' => $batch['nextId'],
                    ]);
            }
        }
        return view('module::TagManager.View.admin.build.index', [
            'bizList' => TagManagerBiz::all(),
        ]);
    }
}
