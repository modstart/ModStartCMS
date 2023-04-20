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
            <form action="{{modstart_api_url('member_money/charge/submit')}}" method="post" data-ajax-form>
                <div class="ub-form">
                    <div class="line">
                        <div class="label">充值金额</div>
                        <div class="field">
                            <input class="form" type="number" step="any" name="money"/>
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
        </div>
    </div>

    <div class="ub-panel">
        <div class="head">
            <div class="title">充值说明</div>
        </div>
        <div class="body">
            <div class="margin-bottom">
                @include('module::PayCenter.View.pc.pay.types')
            </div>
            @if(modstart_config('Member_MoneyChargeDesc'))
                <div class="tw-rounded-lg tw-p-3" style="background:#F8F8F8;">
                    <div class="ub-html">
                        {!! modstart_config('Member_MoneyChargeDesc') !!}
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
