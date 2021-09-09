<?php


namespace Module\Cms\Api\Controller;


use Illuminate\Routing\Controller;
use ModStart\Core\Input\Response;
use Module\Cms\Util\ChannelUtil;
use Module\Cms\Util\PostUtil;

class IndexController extends Controller
{
    public function home()
    {
        $channelTree = ChannelUtil::tree();
        $channelLatestPost = [];
        foreach ($channelTree as $channel) {
            $channelLatestPost[$channel['id']] = PostUtil::latestPostsByChannel($channel['id']);
        }
        $viewData = [];
        $viewData['channelTree'] = $channelTree;
        $viewData['channelLatestPost'] = $channelLatestPost;
        $viewData['latestPosts'] = PostUtil::latestPosts(8);
        return Response::generateSuccessData($viewData);
    }
}
