@extends($_viewMemberFrame)

@section('pageTitleMain','我的钱包')

@section('memberBodyContent')

    <div class="ub-panel transparent">
        <div class="body">
            <div class="row">
                <div class="col-md-6">
                    <div class="ub-dashboard-item-a">
                        <div class="icon">
                            <i class="font iconfont icon-pay"></i>
                        </div>
                        <div class="number-value">￥{{\Module\Member\Util\MemberMoneyUtil::getTotal(\Module\Member\Auth\MemberUser::id())}}</div>
                        <div class="number-title">我的钱包</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="ub-panel" style="margin-top:-0.5rem;">
        <div class="head">
            @if(\ModStart\Module\ModuleManager::getModuleConfigBoolean('Member','moneyCashEnable',false))
                <div class="more">
                    @if(modstart_config('Member_MoneyCashEnable',false))
                        <a href="{{modstart_web_url('member_money/cash')}}">提现申请</a>
                        <a href="{{modstart_web_url('member_money/cash/log')}}">提现记录</a>
                    @endif
                </div>
            @endif
            <div class="title">记录</div>
        </div>
        <div class="body">
            {!! $content !!}
        </div>
    </div>
@endsection
