<div class="tw-bg-white tw-p-4 tw-border tw-border-solid tw-border-gray-200 tw-rounded tw-flex tw-justify-between tw-items-start tw-mb-2">
    <div class="tw-flex">
        <div class="tw-mr-4">
            <div class="ub-cover-1-1 tw-shadow tw-w-20 tw-h-20 tw-rounded"
                 style="background-image:url({{\ModStart\Core\Assets\AssetsUtil::fix($item->cover)}});">
            </div>
        </div>
        <div>
            <div class="tw-font-bold tw-text-gray-700">
                <a href="{{$item->_url}}" target="_blank">{{$item->title}}</a>
                @if($item->verifyStatus==\Module\Cms\Type\CmsContentVerifyStatus::VERIFYING)
                    <span class="tw-font-normal ub-text-warning">审核中</span>
                @elseif($item->verifyStatus==\Module\Cms\Type\CmsContentVerifyStatus::VERIFY_PASS)
                    <span class="tw-font-normal ub-text-muted">审核通过</span>
                @elseif($item->status==\Module\Cms\Type\CmsContentVerifyStatus::VERIFY_PASS)
                    <span class="tw-font-normal ub-text-danger">审核拒绝</span>
                @endif
            </div>
            <div class="tw-text-gray-400 tw-text-sm">时间：{{$item->created_at}}</div>
        </div>
    </div>
    <div class="tw-text-right" style="width:15rem;">
        <div class="tw-mt-2">
            <a class="btn" href="{{modstart_web_url('cms_member_content/edit?id='.$item->id)}}"><i class="iconfont icon-edit"></i>修改</a>
            <a class="btn btn-danger" href="javascript:;" data-confirm="确定删除？" data-ajax-request="{{modstart_api_url('cms_member_content/delete',['id'=>$item->id])}}"><i class="iconfont icon-trash"></i>删除</a>
        </div>
    </div>
</div>
