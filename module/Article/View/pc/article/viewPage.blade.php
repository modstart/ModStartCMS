@extends($_viewFrame)

@section('pageTitleMain'){{$article['title']}}@endsection
@section('pageKeywords'){{$article['title']}}@endsection
@section('pageDescription'){{$article['title']}}@endsection

@section('body')

    <div style="max-width:800px;margin:0 auto;">
        <div class="ub-panel">
            <div class="head"></div>
            <div class="body">
                <div class="ub-article">
                    <h1 class="ub-text-center">{{$article['title']}}</h1>
                    <div class="attr"></div>
                    <div class="content ub-html lg">
                        {!! $article['content'] !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

