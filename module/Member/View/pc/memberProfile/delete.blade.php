@extends($_viewMemberFrame)

@section('pageTitleMain')注销账号@endsection
@section('pageKeywords')注销账号@endsection
@section('pageDescription')注销账号@endsection

@section('memberBodyContent')

    @include('module::Member.View.pc.memberProfile.securityNav')

    <div class="tw-px-3 tw-py-20 tw-rounded-b-lg tw-bg-white">
        <form action="{{\ModStart\Core\Input\Request::currentPageUrl()}}" data-ajax-form class="ub-form" method="post">
            <div class="line">
                <div class="label">用户ID：</div>
                <div class="field">
                    {{ $_memberUser['id'] }}
                </div>
            </div>
            <div class="line">
                <div class="label">注册时间：</div>
                <div class="field">
                    {{ $_memberUser['created_at'] }}
                </div>
            </div>
            @if(!empty($_memberUser['username']))
                <div class="line">
                    <div class="label">用户名：</div>
                    <div class="field">
                        {{ $_memberUser['username'] }}
                    </div>
                </div>
            @endif
            @if($_memberUser['deleteAtTime']>0)
                <div class="line">
                    <div class="label"></div>
                    <div class="field">
                        <div class="ub-alert danger">
                            您的账号将于 {{date('Y-m-d H:i:s',$_memberUser['deleteAtTime'])}} 删除，期间您还可以继续使用
                        </div>
                    </div>
                </div>
                <div class="line">
                    <div class="label">&nbsp;</div>
                    <div class="field">
                        <a class="btn btn-danger" data-ajax-request-loading data-ajax-request="{{modstart_api_url('member_profile/delete_revert')}}">撤销注销账号申请</a>
                    </div>
                </div>
            @else
                <div class="line">
                    <div class="label">二次确认：</div>
                    <div class="field">
                        <div class="ub-alert danger">
                            注销账号申请后30天内账号将会删除，删除后账号所有相关数据也会被删除
                        </div>
                        <label>
                            <input type="checkbox" name="agree" value="yes" />
                            我已知晓
                        </label>
                    </div>
                </div>
                <div class="line">
                    <div class="label">&nbsp;</div>
                    <div class="field">
                        <button type="submit" class="btn btn-danger">申请注销账号</button>
                    </div>
                </div>
            @endif
        </form>
    </div>
@endsection
