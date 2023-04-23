@extends($_viewFrame)

@section('pageTitleMain'){{$article['title']}}@endsection
@section('pageKeywords'){{$article['title']}}@endsection
@section('pageDescription'){{$article['title']}}@endsection

@section('bodyContent')

    <div class="ub-container margin-top">
        <div class="row">
            <div class="col-md-3">
                <div class="ub-menu simple">
                    @foreach(\Module\Article\Util\ArticleUtil::listByPositionWithCache($article['position']) as $item)
                        <a class="title" href="{{$__msRoot}}article/{{$item['id']}}">{{$item['title']}}</a>
                    @endforeach
                </div>
            </div>
            <div class="col-md-9">
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
        </div>
    </div>

@endsection

