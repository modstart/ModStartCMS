@extends('module::CmsThemeCorp.View.pc.cms.frame')

@section('bodyContent')

    <main>
        <div class="page-section pt-4">
            <div class="container">
                <nav aria-label="Breadcrumb">
                    <ol class="breadcrumb bg-transparent mb-4">
                        <li class="breadcrumb-item"><a href="{{modstart_web_url('')}}">首页</a></li>
                        @foreach($catChain as $i=>$c)
                            <li class="breadcrumb-item"><a href="{{modstart_web_url($c['url'])}}">{{$c['title']}}</a></li>
                        @endforeach
                        <li class="breadcrumb-item active" aria-current="page"><a href="javascript:;" class="active">{{$record['title']}}</a></li>
                    </ol>
                </nav>
                <div class="row">
                    <div class="col-lg-8">
                        <div class="blog-single-wrap">
                            <div class="post-thumbnail">
                                <img src="{{\ModStart\Core\Assets\AssetsUtil::fixOrDefault($record['cover'],'vendor/CmsThemeCorp/img/bg_image_1.jpg')}}" alt="">
                            </div>
                            <h1 class="post-title">{{$record['title']}}</h1>
                            <div class="post-meta">
                                <div class="post-author">
                                    <span class="text-grey">By</span> {{$record['author']}}
                                </div>
                                <span class="gap">|</span>
                                <div class="post-date">
                                    {{$record['postTime']}}
                                </div>
{{--                                <span class="gap">|</span>--}}
{{--                                <div>--}}
{{--                                    <a href="@asset('#')">StreetStyle</a>, <a href="@asset('#')">Fashion</a>, <a href="@asset('#')">Couple</a>--}}
{{--                                </div>--}}
{{--                                <span class="gap">|</span>--}}
{{--                                <div class="post-comment-count">--}}
{{--                                    <a href="@asset('#')">8 Comments</a>--}}
{{--                                </div>--}}
                            </div>
                            <div class="post-content">
                                <div class="content ub-html" style="font-size:0.8rem;">
                                    {!! \ModStart\Core\Util\HtmlUtil::replaceImageSrcToLazyLoad($record['_data']['content'],'data-src',true) !!}
                                </div>
                                <div class="post-tags">
                                    <p class="mb-2">标签:</p>
                                    @foreach($record['_tags'] as $tag)
                                        <a href="javascript:;" class="tag-link">{{$tag}}</a>
                                    @endforeach
                                </div>
                            </div>
                        </div> <!-- .blog-single-wrap -->

                    </div>

                    <div class="col-lg-4">
                        <div class="widget">
{{--                            --}}
{{--                            <div class="widget-box">--}}
{{--                                <h3 class="widget-title">Search</h3>--}}
{{--                                <div class="divider"></div>--}}
{{--                                <form action="#" class="search-form">--}}
{{--                                    <div class="form-group">--}}
{{--                                        <input type="text" class="form-control" placeholder="Type a keyword and hit enter">--}}
{{--                                        <button type="submit" class="btn"><span class="icon mai-search"></span></button>--}}
{{--                                    </div>--}}
{{--                                </form>--}}
{{--                            </div>--}}
{{--                            <div class="widget-box">--}}
{{--                                <h3 class="widget-title">Categories</h3>--}}
{{--                                <div class="divider"></div>--}}
{{--                                <ul class="categories">--}}
{{--                                    <li><a href="@asset('#')">Food <span>12</span></a></li>--}}
{{--                                    <li><a href="@asset('#')">Dish <span>22</span></a></li>--}}
{{--                                    <li><a href="@asset('#')">Desserts <span>37</span></a></li>--}}
{{--                                    <li><a href="@asset('#')">Drinks <span>42</span></a></li>--}}
{{--                                    <li><a href="@asset('#')">Ocassion <span>14</span></a></li>--}}
{{--                                </ul>--}}
{{--                            </div>--}}

                            <div class="ub-panel">
                                <div class="head">
                                    <div class="title">
                                        最新发布
                                    </div>
                                </div>
                                <div class="body ub-list-items">
                                    @foreach(\MCms::latestCat($cat['id']) as $a)
                                        <a class="item-c" href="{{$a['_url']}}">{{$a['title']}}</a>
                                    @endforeach
                                </div>
                            </div>
                            <div class="widget-box">
                                <h3 class="widget-title">最新发布</h3>
                                <div class="divider"></div>
                                @foreach(\MCms::latestCat($cat['id']) as $a)
                                    <div class="blog-item">
                                        <div class="content">
                                            <h6 class="post-title"><a href="{{$a['_url']}}">{{$a['title']}}</a></h6>
                                            <div class="meta">
                                                <span class="mai-calendar"></span> {{$a['postTime']}}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

{{--                            <div class="widget-box">--}}
{{--                                <h3 class="widget-title">Tag Cloud</h3>--}}
{{--                                <div class="divider"></div>--}}
{{--                                <div class="tagcloud">--}}
{{--                                    <a href="@asset('#')" class="tag-cloud-link">dish</a>--}}
{{--                                    <a href="@asset('#')" class="tag-cloud-link">menu</a>--}}
{{--                                    <a href="@asset('#')" class="tag-cloud-link">food</a>--}}
{{--                                    <a href="@asset('#')" class="tag-cloud-link">sweet</a>--}}
{{--                                    <a href="@asset('#')" class="tag-cloud-link">tasty</a>--}}
{{--                                    <a href="@asset('#')" class="tag-cloud-link">delicious</a>--}}
{{--                                    <a href="@asset('#')" class="tag-cloud-link">desserts</a>--}}
{{--                                    <a href="@asset('#')" class="tag-cloud-link">drinks</a>--}}
{{--                                </div>--}}
{{--                            </div>--}}

{{--                            <div class="widget-box">--}}
{{--                                <h3 class="widget-title">Paragraph</h3>--}}
{{--                                <div class="divider"></div>--}}
{{--                                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ducimus itaque, autem necessitatibus voluptate quod mollitia delectus aut, sunt placeat nam vero culpa sapiente consectetur similique, inventore eos fugit cupiditate numquam!</p>--}}
{{--                            </div>--}}

                        </div>
                    </div>

                </div>
            </div> <!-- .container -->
        </div> <!-- .page-section -->
    </main>

@endsection
