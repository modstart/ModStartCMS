<?php


namespace Module\Vendor\Provider\SuperSearch\Controller;


use Illuminate\Routing\Controller;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
use Module\Vendor\Provider\SuperSearch\AbstractSuperSearchProvider;
use Module\Vendor\Provider\SuperSearch\SuperSearchBiz;

abstract class AbstractAdminSuperSearchController extends Controller
{
    /**
     * @param AbstractSuperSearchProvider $provider
     * @param array $bizList
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     * @throws BizException
     */
    public function renderIndex($provider, $bizList)
    {
        $bizList = array_map(function ($biz) {
            return SuperSearchBiz::get($biz);
        }, $bizList);
        $bizList = array_filter($bizList);
        if (Request::isPost()) {
            $input = InputPackage::buildFromInput();
            $bucket = $input->getTrimString('bucket');
            $action = $input->getTrimString('action');
            $biz = SuperSearchBiz::get($bucket);
            BizException::throwsIfEmpty('Bucket错误', $biz);
            switch ($action) {
                case 'refresh':
                    return Response::generateSuccessData([
                        'count' => $provider->bucketCount($bucket),
                    ]);
                case 'sync':
                    $nextId = $input->getInteger('nextId', 0);
                    if (0 === $nextId) {
                        $provider->bucketDelete($bucket);
                        $provider->bucketCreate($bucket, $biz->fields());
                    }
                    $ret = $biz->syncBatch($provider, $nextId);
                    $data = [];
                    $data['count'] = $ret['count'];
                    $data['nextId'] = $ret['nextId'];
                    return Response::generateSuccessData($data);
            }
        }
        return view('module::Vendor.View.superSearch.admin.index', [
            'provider' => $provider,
            'bizList' => $bizList,
        ]);
    }
}
