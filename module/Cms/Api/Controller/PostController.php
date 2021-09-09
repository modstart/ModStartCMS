<?php


namespace Module\Cms\Api\Controller;


use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\HtmlUtil;
use ModStart\Core\Util\TagUtil;
use Module\Cms\Event\PostLikedEvent;
use Module\Cms\Util\PostUtil;
use Module\Member\Auth\MemberUser;
use Module\Member\Support\MemberLoginCheck;
use Module\Member\Util\MemberUtil;

class PostController extends Controller implements MemberLoginCheck
{
    public static $memberLoginCheckIgnores = ['get'];

    public function get()
    {
        $input = InputPackage::buildFromInput();
        $alias = $input->getTrimString('alias');
        $post = PostUtil::getByAlias($alias);
        if (empty($post)) {
            return Response::generate(-1, '文章不存在');
        }
        if (!empty($post['memberUserId'])) {
            $memberUser = MemberUtil::get($post['memberUserId']);
            $post['_memberUser'] = array_merge(MemberUtil::convertOneToBasic($memberUser), [
                'cmsWordCount' => $memberUser['cmsWordCount'],
            ]);
        } else {
            $post['_memberUser'] = null;
        }
        $post['_isLike'] = MemberUser::isLogin() && PostUtil::isLiked(MemberUser::id(), $post['id']);

        $d = HtmlUtil::extractTextAndImages($post['contentHtml']);
        $post['_summary'] = Str::limit($d['text'], 200);
        $post['tags'] = TagUtil::string2Array($post['tags']);
        PostUtil::update($post['id'], [
            'viewCount' => intval($post['viewCount']) + 1
        ]);
        return Response::generateSuccessData([
            'post' => $post,
        ]);
    }



}
