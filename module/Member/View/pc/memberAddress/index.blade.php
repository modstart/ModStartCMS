@extends($_viewMemberFrame)

@section('pageTitleMain','我的地址')

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
