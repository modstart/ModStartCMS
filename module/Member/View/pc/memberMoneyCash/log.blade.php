@extends($_viewMemberFrame)

@section('pageTitleMain')提现记录@endsection
@section('pageKeywords')提现记录@endsection
@section('pageDescription')提现记录@endsection

@section('memberBodyContent')

    <div class="ub-breadcrumb tw-bg-white margin-bottom tw-rounded">
        <div class="tw-mx-2">
            <a href="{{modstart_web_url('member_money')}}">我的钱包</a>
            <a class="active" href="{{modstart_web_url('member_money/cash/log')}}">提现记录</a>
        </div>
    </div>

    <div class="ub-panel">
        <div class="head">
            <div class="title">提现申请</div>
        </div>
        <div class="body">
            {!! $content !!}
        </div>
    </div>
@endsection
