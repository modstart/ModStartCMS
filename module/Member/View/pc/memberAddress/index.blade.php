@extends($_viewMemberFrame)

@section('pageTitleMain')我的地址@endsection
@section('pageKeywords')我的地址@endsection
@section('pageDescription')我的地址@endsection

@section('memberBodyContent')
    <div class="ub-panel">
        <div class="head">
            <div class="title">我的地址</div>
        </div>
        <div class="body">
            {!! $content !!}
        </div>
    </div>
@endsection
