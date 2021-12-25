@extends('module::CmsThemeCorp.View.pc.cms.frame')

@section('bodyContent')

    <header>
        <div class="page-banner bg-img bg-img-parallax overlay-dark" style="background-image: url(@asset('vendor/CmsThemeCorp/img/bg_image_3.jpg'));">
            <div class="container h-100">
                <div class="row justify-content-center align-items-center h-100">
                    <div class="col-lg-8">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb breadcrumb-dark bg-transparent justify-content-center py-0">
                                <li class="breadcrumb-item"><a href="{{modstart_web_url('')}}">首页</a></li>
                                <li class="breadcrumb-item active" aria-current="{{modstart_web_url('news')}}">新闻</li>
                            </ol>
                        </nav>
                        <h1 class="fg-white text-center">新闻</h1>
                    </div>
                </div>
            </div>
        </div> <!-- .page-banner -->
    </header>

    <main>
        <div class="page-section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10">

                        @if(empty($records))
                            <div class="ub-empty tw-my-20">
                                <div class="icon">
                                    <div class="iconfont icon-empty-box"></div>
                                </div>
                                <div class="text">暂无记录</div>
                            </div>
                        @else
                            <div class="row">
                                @foreach($records as $record)
                                    <div class="col-md-6 col-lg-4 py-3">
                                        <div class="card-blog">
                                            <div class="cover ub-cover-4-3"
                                                 style="background-image:url({{\ModStart\Core\Assets\AssetsUtil::fix($record['cover'])}})"></div>
                                            <div class="body">
                                                <div class="post-title"><a href="{{$record['_url']}}">{{$record['title']}}</a></div>
                                                <div class="post-excerpt">
                                                    {{\ModStart\Core\Util\HtmlUtil::text($record['summary'],200)}}
                                                </div>
                                            </div>
                                            <div class="footer">
                                                <a href="{{$record['_url']}}">查看更多 <span class="mai-chevron-forward text-sm"></span></a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="ub-page">
                                {!! $pageHtml !!}
                            </div>
                        @endif

                    </div>

                </div>
            </div> <!-- .container -->
        </div> <!-- .page-section -->
    </main>

@endsection
