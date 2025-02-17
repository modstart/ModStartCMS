@extends($_viewFrame)

@section('pageTitleMain'){{$article['title']}}@endsection
@section('pageKeywords'){{$article['title']}}@endsection
@section('pageDescription'){{$article['title']}}@endsection

@section('bodyAppend')@endsection

@section('body')

    <div style="max-width:800px;margin:0 auto;">
        <div class="ub-content-box" style="min-height:100vh;">
            <div class="ub-article">
                @if(empty($hideTitle))
                    <h1 class="ub-text-center">{{$article['title']}}</h1>
                    <div class="attr"></div>
                @endif
                <div class="content ub-html lg">
                    {!! $article['content'] !!}
                </div>
            </div>
        </div>
    </div>

@endsection

