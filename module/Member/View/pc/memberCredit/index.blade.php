@extends($_viewMemberFrame)

@section('pageTitleMain')我的积分@endsection
@section('pageKeywords')我的地址@endsection
@section('pageDescription')我的地址@endsection

@section('memberBodyContent')
    <div class="ub-panel transparent">
        <div class="body">
            <div class="ub-dashboard-item-a">
                <div class="icon">
                    <i class="font iconfont icon-credit"></i>
                </div>
                <div class="number-value">{{\Module\Member\Util\MemberCreditUtil::getTotal(\Module\Member\Auth\MemberUser::id())}}</div>
                <div class="number-title">我的积分</div>
            </div>
        </div>
    </div>
    <div class="ub-panel">
        <div class="head">
            <div class="title">历史记录</div>
        </div>
        <div class="body">
            {!! $content !!}
        </div>
    </div>
@endsection
