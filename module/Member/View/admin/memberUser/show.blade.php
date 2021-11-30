@extends('modstart::admin.dialogFrame')

@section('pageTitle')用户「ID={{$record['id']}}」信息@endsection

@section('bodyContent')

    <div class="ub-panel">
        <div class="head">
            <div class="title">基本信息</div>
        </div>
        <div class="body">
            <div class="ub-pair">
                <div class="name">头像</div>
                <div class="value">
                    <img class="tw-w-10 tw-h-10 tw-rounded-full tw-shadow"
                         src="{{\ModStart\Core\Assets\AssetsUtil::fixOrDefault($record['avatar'],'asset/image/avatar.png')}}"/>
                </div>
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
            @if(\ModStart\Module\ModuleManager::getModuleConfigBoolean('Member','moneyEnable',false))
                <div class="ub-pair">
                    <div class="name">余额</div>
                    <div class="value">￥{{\Module\Member\Util\MemberMoneyUtil::getTotal($record['id'])}}</div>
                </div>
            @endif
            @if(\ModStart\Module\ModuleManager::getModuleConfigBoolean('Member','creditEnable',false))
                <div class="ub-pair">
                    <div class="name">积分</div>
                    <div class="value">{{\Module\Member\Util\MemberCreditUtil::getTotal($record['id'])}}</div>
                </div>
            @endif
        </div>
    </div>

    <div class="tw-p-4 tw-bg-white tw-rounded">
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
