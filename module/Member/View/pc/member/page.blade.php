@extends($_viewFrame)

@section('pageTitleMain'){{$pageTitle}}@endsection
@section('pageKeywords'){{$pageTitle}}@endsection
@section('pageDescription'){{$pageTitle}}@endsection

@section('body')

    <div style="max-width:800px;margin:0 auto;">
        <div class="ub-panel">
            <div class="head"></div>
            <div class="body">
                <div class="ub-article">
                    <h1 class="ub-text-center">{{$pageTitle}}</h1>
                    <div class="attr"></div>
                    <div class="content ub-html">
                        {!! $pageContent !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

