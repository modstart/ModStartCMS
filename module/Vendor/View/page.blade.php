@extends($_viewFrame)

@section('pageTitleMain'){{$pageTitle}}@endsection
@section('pageKeywords'){{$pageTitle}}@endsection
@section('pageDescription'){{$pageTitle}}@endsection

@section('body')

    <div class="ub-container margin-top margin-bottom" style="max-width:40rem;">
        <div class="ub-panel">
            <div class="head"></div>
            <div class="body">
                <div class="ub-article">
                    <h1 class="ub-text-center">{{$pageTitle}}</h1>
                    <div class="attr"></div>
                    <div class="content ub-html lg">
                        {!! $pageContent !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

