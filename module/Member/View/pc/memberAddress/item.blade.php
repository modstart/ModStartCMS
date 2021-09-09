<div class="tw-bg-gray-100 tw-rounded-sm tw-mb-2 tw-box tw-px-5 tw-py-3 tw-mb-3 tw-zoom-in" data-repeat="3">
    <div class="tw-items-center">
        <span class="tw-font-medium">
            {{$item->name}}
        </span>
        <span class="tw-ml-2">
            {{$item->phone}}
        </span>
    </div>
    <div class="tw-pt-2 tw-text-gray-400">
        {{$item->area}}
        {{$item->detail}}
        {{$item->post}}
    </div>
    <div class="tw-text-right">
        <a href="javascript:;"
           data-dialog-request="{{action('\Module\Member\Web\Controller\MemberAddressController@edit',['_id'=>$item->id])}}"
           class="ub-lister-action">编辑</a>
        <a href="javascript:;"
           data-ajax-request="{{action('\Module\Member\Web\Controller\MemberAddressController@delete',['_id'=>$item->id])}}"
           data-confirm="确定删除？"
           class="ub-lister-action danger">删除</a>
    </div>
</div>
