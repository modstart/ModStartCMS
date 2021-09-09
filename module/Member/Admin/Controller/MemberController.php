<?php


namespace Module\Member\Admin\Controller;

use Illuminate\Routing\Controller;
use ModStart\Admin\Concern\HasAdminQuickCRUD;
use ModStart\Admin\Layout\AdminCRUDBuilder;
use ModStart\Admin\Layout\AdminDialogPage;
use ModStart\Core\Assets\AssetsUtil;
use ModStart\Core\Input\InputPackage;
use ModStart\Core\Input\Request;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\CRUDUtil;
use ModStart\Grid\GridFilter;
use ModStart\Support\Concern\HasFields;
use Module\Member\Type\MemberStatus;
use Module\Member\Util\MemberUtil;

class MemberController extends Controller
{
    use HasAdminQuickCRUD;

    protected function crud(AdminCRUDBuilder $builder)
    {
        $builder
            ->init('member_user')
            ->field(function ($builder) {
                
                $builder->id('id', 'ID');
                $builder->display('created_at', '创建时间');
                $builder->type('status', '状态')->type(MemberStatus::class);
                $builder->image('avatar', '头像');
                $builder->text('username', '用户名');
                $builder->text('email', '邮箱');
                $builder->text('phone', '手机');
            })
            ->gridFilter(function (GridFilter $filter) {
                $filter->eq('id', L('ID'));
                $filter->eq('status', '状态')->select(MemberStatus::class);
                $filter->like('username', '用户名');
                $filter->like('email', '邮箱');
                $filter->like('phone', '手机');
            })
            ->title('用户管理')
            ->canDelete(false);
    }

    public function select(AdminDialogPage $page)
    {
        $grid = $this->grid();
        $grid->disableCUD();
        $grid->canSingleSelectItem(true);
        CRUDUtil::registerGridResource($grid, '\\' . __CLASS__);
        if (Request::isPost()) {
            return $grid->request();
        }
        return $page->pageTitle('选择用户')->body($grid);
    }

    public function search()
    {
        $input = InputPackage::buildFromInput();
        $keywords = $input->getTrimString('keywords');
        $option = [];
        $option['whereOperate'] = ['username', 'like', "%$keywords%"];
        $paginateData = MemberUtil::paginate(1, 10, $option);
        $records = array_map(function ($item) {
            return [
                'value' => intval($item['id']),
                'name' => htmlspecialchars(MemberUtil::viewName($item)),
                'avatar' => AssetsUtil::fixOrDefault($item['avatar'], 'asset/image/avatar.png'),
            ];
        }, $paginateData['records']);
        return Response::jsonSuccessData($records);
    }
}
