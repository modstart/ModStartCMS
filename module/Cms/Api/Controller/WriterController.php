<?php


namespace Module\Cms\Api\Controller;


use Illuminate\Routing\Controller;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\CRUDUtil;
use ModStart\Core\Util\HtmlUtil;
use ModStart\Core\Util\RandomUtil;
use ModStart\Core\Util\TagUtil;
use Module\Cms\Type\PostEditorType;
use Module\Cms\Util\PostUtil;
use Module\Cms\Util\WriterUtil;
use Module\Member\Auth\MemberUser;
use Module\Member\Support\MemberLoginCheck;
use Module\Member\Util\MemberUtil;
use Module\Vendor\Html\HtmlConverter;

class WriterController extends Controller implements MemberLoginCheck
{
    public function settingGet()
    {
        $memberUser = MemberUser::user();
        $setting = [
            'cmsEditorType' => $memberUser['cmsEditorType'],
        ];
        if (empty($setting['cmsEditorType'])) {
            $setting['cmsEditorType'] = modstart_config()->getInteger('Cms_PostDefaultEditorType', PostEditorType::RICH_TEXT);
        }
        return Response::generateSuccessData($setting);
    }

    public function settingSave()
    {
        $input = InputPackage::buildFromInput();
        $update = [];
        $update['cmsEditorType'] = $input->getType('cmsEditorType', PostEditorType::class);
        MemberUtil::update(MemberUser::id(), $update);
        return Response::generateSuccess('保存成功');
    }

    public function categoryAll()
    {
        return Response::generateSuccessData(WriterUtil::categoryAll(MemberUser::id()));
    }


    public function postGet()
    {
        $id = InputPackage::buildFromInput()->getInteger('id');
        $memberPost = ModelUtil::get('cms_member_post', [
            'id' => $id, 'memberUserId' => MemberUser::id(),
        ]);
        BizException::throwsIfEmpty('文章不存在', $memberPost);
        $publishPostAlias = null;
        if ($publishPost = ModelUtil::get('cms_post', ['memberPostId' => $memberPost['id'], 'isDeleted' => false])) {
            $publishPostAlias = $publishPost['alias'];
        }
        return Response::generateSuccessData([
            'memberPost' => [
                'id' => $memberPost['id'],
                'categoryId' => $memberPost['categoryId'],
                'title' => $memberPost['title'],
                'isPublished' => boolval($memberPost['isPublished']),
                'isOriginal' => boolval($memberPost['isOriginal']),
                'contentType' => intval($memberPost['contentType']),
                'tags' => join(',', TagUtil::string2Array($memberPost['tags'])),
                'content' => $memberPost['content'],
                '_publishPostAlias' => $publishPostAlias
            ]
        ]);
    }

    public function postDelete()
    {
        $input = InputPackage::buildFromInput();
        $id = $input->getInteger('id');
        if (!$id) {
            $id = CRUDUtil::id();
        }
        $memberPost = ModelUtil::get('cms_member_post', [
            'id' => $id, 'memberUserId' => MemberUser::id()
        ]);
        BizException::throwsIfEmpty('文章不存在', $memberPost);
        ModelUtil::delete('cms_member_post', ['id' => $memberPost['id']]);
        ModelUtil::delete('cms_member_post_history', ['memberPostId' => $memberPost['id']]);
        PostUtil::deleteByMemberPostId($memberPost['id']);
        return Response::generate(0, null);
    }

    public function postEdit()
    {
        $input = InputPackage::buildFromInput();
        $id = $input->getInteger('id');
        $memberPost = null;
        if ($id) {
            $memberPost = ModelUtil::get('cms_member_post', [
                'id' => $id, 'memberUserId' => MemberUser::id()
            ]);
            BizException::throwsIfEmpty('文章不存在', $memberPost);
        }

        $data = [];
        $data['categoryId'] = $input->getInteger('categoryId');
        $data['title'] = $input->getTrimString('title');
        $data['contentType'] = $input->getType('contentType', PostEditorType::class, PostEditorType::RICH_TEXT);
        $data['isOriginal'] = $input->getBoolean('isOriginal', false);
        $data['tags'] = TagUtil::seperated2String($input->getTrimString('tags'));
        switch ($data['contentType']) {
            case PostEditorType::RICH_TEXT:
                $data['content'] = $input->getRichContent('content');
                $data['content'] = HtmlUtil::filter2($data['content']);
                break;
            case PostEditorType::MARKDOWN:
                $data['content'] = $input->getTrimString('content');
                break;
            default:
                return Response::generateError('错误的文章类型');
        }
        if (empty($data['title'])) {
            return Response::generate(-1, '标题不能为空');
        }
        if (empty($data['content'])) {
            return Response::generate(-1, '内容不能为空');
        }
        if ($memberPost) {
                        if ($memberPost['title'] != $data['title'] || $memberPost['content'] != $data['content']) {
                ModelUtil::insert('cms_member_post_history', [
                    'memberPostId' => $memberPost['id'],
                    'title' => $memberPost['title'],
                    'content' => $memberPost['content'],
                ]);

                $limit = 50;
                $oldHistory = ModelUtil::model('cms_member_post_history')
                    ->where(['memberPostId' => $memberPost['id'],])
                    ->select('id')
                    ->orderBy('id', 'desc')
                    ->limit($limit)
                    ->get()
                    ->toArray();
                $minId = $oldHistory[count($oldHistory) - 1]['id'];
                ModelUtil::model('cms_member_post_history')
                    ->where(['memberPostId' => $memberPost['id'],])
                    ->where('id', '<', $minId)
                    ->delete();
            }
            ModelUtil::update('cms_member_post', ['id' => $id], $data);
        } else {
            $data['memberUserId'] = MemberUser::id();
            $data['isPublished'] = false;
            $memberPost = ModelUtil::insert('cms_member_post', $data);
        }
        $memberPost = ModelUtil::get('cms_member_post', ['id' => $memberPost['id']]);
        $publishPostAlias = null;
        if ($publishPost = ModelUtil::get('cms_post', ['memberPostId' => $memberPost['id'], 'isDeleted' => false])) {
            $publishPostAlias = $publishPost['alias'];
        }
        return Response::generate(0, null, [
            'memberPost' => [
                'id' => $memberPost['id'],
                'categoryId' => $memberPost['categoryId'],
                'title' => $memberPost['title'],
                'isPublished' => boolval($memberPost['isPublished']),
                'contentType' => intval($memberPost['contentType']),
                'content' => $memberPost['content'],
                'tags' => $memberPost['tags'],
                '_publishPostAlias' => $publishPostAlias
            ]
        ]);
    }

    private function updateMemberUserPostStat()
    {
        $postCount = ModelUtil::count('cms_post', ['memberUserId' => MemberUser::id(), 'isDeleted' => false]);
        $wordCount = ModelUtil::sum('cms_post', 'wordCount', ['memberUserId' => MemberUser::id(), 'isDeleted' => false]);
        ModelUtil::update('member_user', ['id' => MemberUser::id()], [
            'cmsPostCount' => $postCount,
            'cmsWordCount' => $wordCount,
        ]);
    }

    public function postPublish()
    {
        $input = InputPackage::buildFromInput();
        $id = $input->getInteger('id');
        $memberPost = ModelUtil::get('cms_member_post', ['id' => $id, 'memberUserId' => MemberUser::id()]);
        BizException::throwsIfEmpty('文章不存在', $memberPost);
        $post = ModelUtil::get('cms_post', ['memberPostId' => $memberPost['id']]);
        if (empty($post)) {
            $post['id'] = 0;
            $post['memberUserId'] = MemberUser::id();
            $post['memberPostId'] = $memberPost['id'];
            $post['alias'] = RandomUtil::lowerString(16);
        }
        $post['isDeleted'] = false;
        $post['isOriginal'] = $memberPost['isOriginal'];
        $post['tags'] = $memberPost['tags'];
        $post['title'] = $memberPost['title'];
        $post['contentHtml'] = HtmlConverter::convertToHtml(
            $memberPost['contentType'],
            $memberPost['content']
        );
        $post['wordCount'] = HtmlUtil::workCount($memberPost['content']);
                if ($post['id']) {
            ModelUtil::update('cms_post', ['id' => $post['id']], $post);
            $post = ModelUtil::get('cms_post', $post['id']);
        } else {
            $post = ModelUtil::insert('cms_post', $post);
        }
        ModelUtil::update('cms_member_post', ['id' => $memberPost['id']], ['isPublished' => true]);

        $this->updateMemberUserPostStat();
        return Response::generate(0, '发布成功', [
            'alias' => $post['alias'],
        ], CRUDUtil::jsGridRefresh());
    }

    public function postPublishCancel()
    {
        $input = InputPackage::buildFromInput();
        $id = $input->getInteger('id');
        $memberPost = ModelUtil::get('cms_member_post', ['id' => $id, 'memberUserId' => MemberUser::id()]);
        BizException::throwsIfEmpty('文章不存在', $memberPost);

        $post = ModelUtil::get('cms_post', ['memberPostId' => $memberPost['id']]);
        if (empty($post)) {
            return Response::generate(-1, '发布文章不存在');
        }
        ModelUtil::update('cms_post', ['id' => $post['id']], ['isDeleted' => true]);
        ModelUtil::update('cms_member_post', ['id' => $memberPost['id']], ['isPublished' => false]);
        ModelUtil::delete('cms_post_like', ['postId' => $post['id']]);
        $this->updateMemberUserPostStat();
        return Response::generate(0, '取消发布成功', [], CRUDUtil::jsGridRefresh());
    }

    public function postHistory()
    {
        $input = InputPackage::buildFromInput();
        $id = $input->getInteger('id');
        $memberPost = ModelUtil::get('cms_member_post', ['id' => $id, 'memberUserId' => MemberUser::id()]);
        BizException::throwsIfEmpty('文章不存在', $memberPost);

        $histories = [];
        $records = ModelUtil::all('cms_member_post_history', ['memberPostId' => $memberPost['id']], ['id', 'title', 'created_at', 'content'], ['id', 'desc']);
        foreach ($records as $record) {
            $histories[] = [
                'id' => $record['id'],
                'title' => $record['title'],
                'time' => $record['created_at'],
                'content' => $record['content'],
            ];
        }
        return Response::generateSuccessData([
            'histories' => $histories,
        ]);
    }

    public function postPaginate()
    {
        $input = InputPackage::buildFromInput();
        $page = $input->getPage();
        $pageSize = $input->getPageSize();
        $option = [];
        $option['where'] = [];
        $option['where']['memberUserId'] = MemberUser::id();
        $option['order'] = [];
        $option['order'][] = ['updated_at', 'desc'];

        $categoryId = $input->getInteger('categoryId');
        if ($categoryId) {
            $option['where']['categoryId'] = $categoryId;
        }

        $paginateData = ModelUtil::paginate('cms_member_post', $page, $pageSize, $option);

        $records = [];
        foreach ($paginateData['records'] as $record) {
            $records[] = [
                'id' => $record['id'],
                'title' => $record['title'],
                'time' => $record['updated_at'],
                'summary' => HtmlUtil::text($record['content'], 100),
                'isPublished' => ($record['isPublished'] ? true : false),
            ];
        }

        return Response::generate(0, null, [
            'page' => $page,
            'pageSize' => $pageSize,
            'records' => $records,
        ]);
    }


}
