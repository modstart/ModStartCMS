<?php


namespace Module\CmsWriter\Api\Controller;


use Illuminate\Routing\Controller;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\TreeUtil;
use Module\CmsWriter\Util\ChannelUtil;
use Module\CmsWriter\Util\PostUtil;
use Module\Member\Util\MemberUtil;


class ChannelController extends Controller
{
    
    public function paginate()
    {
        $input = InputPackage::buildFromInput();
        $searchInput = $input->getAsInput('search');
        $channelAlias = $input->getTrimString('channelAlias');
        $channel = null;
        if ($channelAlias) {
            $channel = ChannelUtil::getByAlias($channelAlias);
            BizException::throwsIfEmpty('频道不存在', $channel);
        }
        $page = $input->getPage();
        $pageSize = $input->getPageSize();
        $option = [];
        $option['order'] = ['id', 'desc'];
        if ($channel) {
            $nodes = ChannelUtil::all();
            $channelIds = TreeUtil::nodesChildrenIds($nodes, $channel['id']);
            $channelIds = array_merge([$channel['id']], $channelIds);
            $option['order'] = ['id', 'desc'];
            $option['whereIn'] = ['channelId', $channelIds];
        }
        $paginateData = PostUtil::paginatePosts($page, $pageSize, $option);
        MemberUtil::mergeMemberUserBasics($paginateData['records']);
        return Response::generateSuccessData([
            'page' => $page,
            'pageSize' => $pageSize,
            'records' => $paginateData['records'],
            'total' => $paginateData['total'],
            'channel' => $channel,
        ]);
    }
}
