@extends($_viewMemberFrame)

@section('pageTitleMain')钱包充值@endsection
@section('pageKeywords')钱包充值@endsection
@section('pageDescription')钱包充值@endsection

@section('memberBodyContent')

    <div class="ub-breadcrumb tw-bg-white margin-bottom tw-rounded">
        <div class="tw-mx-2">
            <a href="{{modstart_web_url('member_money')}}">我的钱包</a>
            <a class="active" href="{{modstart_web_url('member_money/charge')}}">钱包充值</a>
        </div>
    </div>
    <div class="ub-panel">
        <div class="head">
            <div class="title">钱包充值</div>
        </div>
        <div class="body">
            @if(!modstart_module_enabled('PayCenter'))
                <div class="ub-alert warning">
                    <i class="iconfont icon-warning"></i>
                    请先安装 <a href="https://modstart.com/m/PayCenter" target="_blank">PayCenter</a> 模块
                </div>
            @elseif(\Module\PayCenter\Util\PayUtil::preferShowQuick())
                <div class="ub-form vertical">
                    <div class="line">
                        <div class="label">充值金额</div>
                        <div class="field">
                            <input class="form" type="number" step="any" value="100" name="money"/>
                        </div>
                    </div>
                    <div class="line">
                        <div class="label">扫码支付</div>
                        <div class="field">
                            @include('module::PayCenter.View.inc.quick')
                            <script>
                                $(function () {
                                    var refresh = function () {
                                        var money = parseFloat($('[name="money"]').val());
                                        if (money >= 0.01 && money < 1000 * 10000) {
                                            money = parseFloat(money).toFixed(2)
                                            $('[name="money"]').val(money + '')
                                            window.__payCenterQuick.prepareLazy(
                                                '{{\Module\Member\Core\MemberMoneyChargePayCenterBiz::NAME}}',
                                                {money: money},
                                                {money: money}
                                            );
                                        } else {
                                            window.__payCenterQuick.empty();
                                        }
                                    };
                                    $('[name="money"]').on('keyup', refresh);
                                    refresh();
                                });
                            </script>
                        </div>
                    </div>
                </div>
            @else
                <form action="{{modstart_api_url('member_money/charge/submit')}}" method="post" data-ajax-form>
                    <div class="ub-form">
                        <div class="line">
                            <div class="label">充值金额</div>
                            <div class="field">
                                <input class="form" type="number" step="any" value="100" name="money"/>
                            </div>
                        </div>
                        <div class="line">
                            <div class="label">&nbsp;</div>
                            <div class="field">
                                <input type="hidden" name="recirect" {{modstart_web_url('member_money')}} />
                                <button class="btn btn-primary" type="submit">提交支付</button>
                            </div>
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </div>

    @if(modstart_config('Member_MoneyChargeDesc'))
        <div class="ub-panel">
            <div class="head">
                <div class="title">充值说明</div>
            </div>
            <div class="body">
                <div class="ub-html">
                    {!! modstart_config('Member_MoneyChargeDesc') !!}
                </div>
            </div>
        </div>
    @endif

@endsection
