@extends($_viewMemberFrame)

@section('pageTitleMain')钱包提现@endsection
@section('pageKeywords')钱包提现@endsection
@section('pageDescription')钱包提现@endsection


@section('bodyAppend')
    @parent
    <script>
        $(function () {
            var calc = function () {
                var money = $('[name="money"]').val();
                if (!money) {
                    $('[data-money]').html('-');
                    return;
                }
                window.api.base.postSuccess("{{modstart_api_url('member_money/cash/calc')}}", {money: money}, function (res) {
                    $('[data-money]').html(res.data.value);
                }, function (res) {
                    $('[data-money]').html('<span class="ub-text-danger">' + res.msg + '</span>');
                });
            };
            var timer = null;
            $('[name="money"]').on('change', function () {
                if (timer) {
                    clearTimeout(timer);
                }
                $('[data-money]').html('<span class="ub-text-muted">正在计算...</span>');
                timer = setTimeout(calc, 500);
            });
            var showType = function () {
                var type = $('[name="type"]').val();
                $('[data-type]').addClass('tw-hidden');
                $('[data-type="' + type + '"]').removeClass('tw-hidden');
            };
            $('[name="type"]').on('change', function () {
                showType();
            });
            showType();
        });
    </script>
@endsection

@section('memberBodyContent')

    <div class="ub-breadcrumb tw-bg-white margin-bottom tw-rounded">
        <div class="tw-mx-2">
            <a href="{{modstart_web_url('member_money')}}">我的钱包</a>
            <a class="active" href="{{modstart_web_url('member_money/cash')}}">钱包提现</a>
        </div>
    </div>

    @if(modstart_config('Member_MoneyCashDescription'))
        <div class="ub-panel">
            <div class="head">
                <div class="title">提现说明</div>
            </div>
            <div class="body">
                <div class="ub-html lg">
                    {!! modstart_config('Member_MoneyCashDescription') !!}
                </div>
            </div>
        </div>
    @endif
    <div class="ub-panel">
        <div class="head">
            <div class="more">
                <a class="tw-ml-2" href="{{modstart_web_url('member_money/cash/log')}}">
                    <i class="iconfont icon-time"></i>
                    提现记录
                </a>
            </div>
            <div class="title">钱包提现</div>
        </div>
        <div class="body">
            <form action="{{modstart_api_url('member_money/cash/submit')}}" method="post" data-ajax-form>
                <div class="ub-form">
                    <div class="line">
                        <div class="label">余额</div>
                        <div class="field">
                            ￥{{sprintf('%0.2f',$total)}}
                        </div>
                    </div>
                    @if($total<modstart_config('Member_MoneyCashMin',100))
                        <div class="line">
                            <div class="label">&nbsp;</div>
                            <div class="field">
                                <div class="ub-alert warning">
                                    暂时不能申请，单次提现最小金额为 ￥{{sprintf('%0.2f',modstart_config('Member_MoneyCashMin',100))}}
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="line">
                            <div class="label">提现金额</div>
                            <div class="field">
                                <input class="form" type="number" step="any" name="money"/>
                                <div class="help">
                                    单次提现最小金额为 ￥{{sprintf('%0.2f',modstart_config('Member_MoneyCashMin',100))}}
                                </div>
                            </div>
                        </div>
                        @if(modstart_config('Member_MoneyCashTaxRate',0)>0)
                            <div class="line">
                                <div class="label">手续费</div>
                                <div class="field">
                                    {{sprintf('%0.2f',modstart_config('Member_MoneyCashTaxRate',0))}}%
                                    （实际到账：￥<div class="tw-inline-block ub-text-primary" data-money>-</div>）
                                </div>
                            </div>
                        @endif
                        <div class="line">
                            <div class="label">支付类型</div>
                            <div class="field">
                                <select class="form" name="type">
                                    @foreach(\Module\Member\Type\MemberMoneyCashType::getList() as $k=>$v)
                                        <option value="{{$k}}">{{$v}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="line tw-hidden" data-type="{{\Module\Member\Type\MemberMoneyCashType::ALIPAY}}">
                            <div class="label">姓名</div>
                            <div class="field">
                                <input class="form" type="text" name="alipayRealname" placeholder="请填写真实姓名" />
                            </div>
                        </div>
                        <div class="line tw-hidden" data-type="{{\Module\Member\Type\MemberMoneyCashType::ALIPAY}}">
                            <div class="label">支付宝账号</div>
                            <div class="field">
                                <input class="form" type="text" name="alipayAccount" placeholder="请填写支付宝账号" />
                            </div>
                        </div>
                        <div class="line tw-hidden" data-type="{{\Module\Member\Type\MemberMoneyCashType::WECHAT}}">
                            <div class="label">姓名</div>
                            <div class="field">
                                <input class="form" type="text" name="wechatRealname" placeholder="请填写真实姓名" />
                            </div>
                        </div>
                        <div class="line tw-hidden" data-type="{{\Module\Member\Type\MemberMoneyCashType::WECHAT}}">
                            <div class="label">微信号</div>
                            <div class="field">
                                <input class="form" type="text" name="wechatAccount" placeholder="请填写微信号" />
                            </div>
                        </div>
                        <div class="line tw-hidden" data-type="{{\Module\Member\Type\MemberMoneyCashType::BANK}}">
                            <div class="label">姓名</div>
                            <div class="field">
                                <input class="form" type="text" name="bankRealname" placeholder="请填写真实姓名" />
                            </div>
                        </div>
                        <div class="line tw-hidden" data-type="{{\Module\Member\Type\MemberMoneyCashType::BANK}}">
                            <div class="label">银行账号</div>
                            <div class="field">
                                <input class="form" type="text" name="bankAccount" placeholder="请填写银行账号" />
                            </div>
                        </div>
                        <div class="line tw-hidden" data-type="{{\Module\Member\Type\MemberMoneyCashType::BANK}}">
                            <div class="label">开户行</div>
                            <div class="field">
                                <input class="form" type="text" name="bankName" placeholder="请填写开户行" />
                            </div>
                        </div>
                        <div class="line">
                            <div class="label">&nbsp;</div>
                            <div class="field">
                                <button class="btn btn-round btn-primary" type="submit">提交申请</button>
                            </div>
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>
@endsection
