@extends('modstart::admin.dialogFrame')

@section('pageTitle')用户 {{\Module\Member\Util\MemberUtil::viewName($record)}} 的信息@endsection

@section('headAppend')
    @parent
    <style>
        .ub-panel-dialog .panel-dialog-body{bottom:0;}
        .ub-panel-dialog .panel-dialog-foot{display:none;}
    </style>
@endsection

@section('bodyContent')

    <div class="ub-content-box margin-bottom">
        <div class="ub-pair">
            <div class="name">头像</div>
            <div class="value">
                <img class="tw-w-10 tw-h-10 tw-rounded-full tw-shadow"
                     src="{{\ModStart\Core\Assets\AssetsUtil::fixOrDefault($record['avatar'],'asset/image/avatar.svg')}}"/>
            </div>
        </div>
        <div class="ub-pair">
            <div class="name">用户ID</div>
            <div class="value">{{$record['id']}}</div>
        </div>
        <div class="ub-pair">
            <div class="name">用户名</div>
            <div class="value">{{$record['username']?$record['username']:'-'}}</div>
        </div>
        <div class="ub-pair">
            <div class="name">邮箱</div>
            <div class="value">{{$record['email']?$record['email']:'-'}}</div>
        </div>
        <div class="ub-pair">
            <div class="name">手机</div>
            <div class="value">{{$record['phone']?$record['phone']:'-'}}</div>
        </div>
        <div class="ub-pair">
            <div class="name">昵称</div>
            <div class="value">{{$record['nickname']?$record['nickname']:'-'}}</div>
        </div>
        @if(array_key_exists('gender',$record))
            <div class="ub-pair">
                <div class="name">性别</div>
                <div class="value">{{\ModStart\Core\Type\TypeUtil::name(\Module\Member\Type\Gender::class,$record['gender'])}}</div>
            </div>
        @endif
        @if(\ModStart\Module\ModuleManager::getModuleConfig('Member','moneyEnable',false))
            <div class="ub-pair">
                <div class="name">余额</div>
                <div class="value">
                    <span class="tw-inline-block" style="min-width:4rem;">
                        ￥{{\Module\Member\Util\MemberMoneyUtil::getTotal($record['id'])}}
                    </span>
                    <a href="javascript:;" class="tw-ml-4" data-dialog-request="{{modstart_admin_url('member_money/charge',['memberUserId'=>$record['id']])}}">
                        [余额变更]
                    </a>
                </div>
            </div>
        @endif
        @if(\ModStart\Module\ModuleManager::getModuleConfig('Member','creditEnable',false))
            <div class="ub-pair">
                <div class="name">积分</div>
                <div class="value">
                    <span class="tw-inline-block" style="min-width:4rem;">
                        {{\Module\Member\Util\MemberCreditUtil::getTotal($record['id'])}}
                    </span>
                    <a href="javascript:;" class="tw-ml-4" data-dialog-request="{{modstart_admin_url('member_credit/charge',['memberUserId'=>$record['id']])}}">
                        [积分变更]
                    </a>
                </div>
            </div>
        @endif
        <div class="tw-pt-4 tw-bg-white tw-rounded">
            <a href="javascript:;" class="btn"
               data-dialog-width="90%" data-dialog-height="90%"
               data-dialog-request="{{modstart_admin_url('member/edit',['_id'=>$record['id']])}}">
                <i class="iconfont icon-edit"></i>
                修改信息
            </a>
            <a href="javascript:;" class="btn"
               data-dialog-request="{{modstart_admin_url('member/reset_password',['_id'=>$record['id']])}}">
                <i class="iconfont icon-lock"></i>
                重置密码
            </a>
            <a href="javascript:;" class="btn"
               data-dialog-request="{{modstart_admin_url('member/send_message',['_id'=>$record['id']])}}">
                <i class="iconfont icon-comment"></i>
                发送消息
            </a>
        </div>
    </div>

    @if(!empty($showPanelProviders))
        <div class="ub-nav-tab margin-top" data-show-panel-tab>
            @foreach($showPanelProviders as $provider)
                <a href="javascript:;">{{$provider->title()}}</a>
            @endforeach
        </div>
        <div class="tw-bg-white tw-rounded tw-p-4" data-show-panel-body>
            @foreach($showPanelProviders as $provider)
                <div class="tw-overflow-hidden">
                    {!! $provider->render($record,[]) !!}
                </div>
            @endforeach
        </div>
        <script>
            $(function () {
                var $tab = $('[data-show-panel-tab] > a');
                var $body = $('[data-show-panel-body] > div');
                $tab.on('click', function () {
                    var index = $tab.index(this)
                    $tab.removeClass('active');
                    $(this).addClass('active');
                    $body.css({width: 0, height: 0});
                    $($body.get(index)).css({width: 'auto', height: 'auto'});
                });
                $($tab[0]).click();
            });
        </script>
    @endif

@endsection
