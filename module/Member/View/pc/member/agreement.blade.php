@extends($_viewFrame)

@section('pageTitleMain'){{modstart_config('Member_AgreementTitle','用户使用协议')}}@endsection

@section('body')

    <div style="max-width:800px;margin:0 auto;">
        <div class="ub-panel">
            <div class="head"></div>
            <div class="body">
                <div class="ub-article">
                    <h1 class="ub-text-center">{{modstart_config('Member_AgreementTitle','用户使用协议')}}</h1>
                    <div class="attr"></div>
                    <div class="content ub-html">
                        {!! modstart_config('Member_AgreementContent') !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

