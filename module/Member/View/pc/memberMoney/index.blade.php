@extends($_viewMemberFrame)

@section('pageTitleMain')我的钱包@endsection
@section('pageKeywords')我的钱包@endsection
@section('pageDescription')我的钱包@endsection

@section('memberBodyContent')

    <div class="ub-panel transparent">
        <div class="body">
            <div class="row">
                <div class="col-md-12">
                    <div class="ub-dashboard-item-a">
                        <div class="icon">
                            <i class="font iconfont icon-pay"></i>
                        </div>
                        <div class="number-value">
                            ￥{{\Module\Member\Util\MemberMoneyUtil::getTotal(\Module\Member\Auth\MemberUser::id())}}</div>
                        <div class="number-title">我的钱包</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="ub-panel" style="margin-top:-0.5rem;">
        <div class="head">
            <div class="more">
                @if(modstart_config('Member_MoneyChargeEnable',false))
                    <a class="tw-ml-2" href="{{modstart_web_url('member_money/charge')}}">
                        <i class="iconfont icon-cny"></i>
                        充值
                    </a>
                @endif
                @if(modstart_config('Member_MoneyCashEnable',false))
                    <a class="tw-ml-2" href="{{modstart_web_url('member_money/cash')}}">
                        <i class="iconfont icon-pay"></i>
                        提现
                    </a>
                @endif
            </div>
            <div class="title">
                <i class="iconfont icon-list"></i>
                钱包流水
            </div>
        </div>
        <div class="body">
            {!! $content !!}
        </div>
    </div>
@endsection
