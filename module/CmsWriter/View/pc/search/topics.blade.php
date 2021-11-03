@extends($_viewFrame)

@section('pageTitleMain')搜索「{{$keywords}}」结果@endsection

{!! \ModStart\ModStart::styleFile('public/vendor/CmsWriter/asset/notesns.css') !!}
{!! \ModStart\ModStart::scriptFile('public/vendor/CmsWriter/asset/notesns.js') !!}
{!! \ModStart\ModStart::styleFile('public/vendor/MemberFollow/asset/memberFollow.css') !!}
{!! \ModStart\ModStart::scriptFile('public/vendor/MemberFollow/asset/memberFollow.js') !!}
{!! \ModStart\ModStart::js('asset/vendor/jqueryMark.js') !!}
@section('bodyAppend')
    @parent
    <style type="text/css">
        mark {
            color: red;
            background: none;
        }
    </style>
    <script>
        $(function () {
            $("[data-search-result] a").mark({!! json_encode($keywords) !!}.split('').join(' '),{separateWordSearch:true});
        });
    </script>
@endsection

@section('bodyContent')

    <div class="ub-container">

        <div class="row">
            <div class="col-md-3">
                @include('module::CmsWriter.View.pc.search.tab')
            </div>
            <div class="col-md-9">

                <div class="ub-padding tw-bg-white margin-top">
                    <div class="ub-search-block-a">
                        <input class="input" type="text" placeholder="搜索 文章/专题/用户"
                               value="{{$keywords}}"
                               id="keyword" onkeypress="if(event.keyCode===13){window.location.href='{{modstart_web_url('search/notes')}}?keywords='+window.api.util.urlencode($(this).val());}" />
                        <a class="search-btn" href="javascript:;"
                           onclick="window.location.href='{{modstart_web_url('search/notes')}}?keywords='+window.api.util.urlencode($(this).prev().val());">
                            <span class="iconfont icon-search"></span>
                            搜索
                        </a>
                    </div>
                </div>

                <div class="ub-search-result">
                    搜索 <span class="keyword">{{$keywords}}</span> ，共找到 <span class="count">{{$total}}</span> 条记录
                </div>

                <div class="tw-bg-white tw-rounded">
                    <div class="ub-nav-tab margin-top">
                        <a href="javascript:;" class="active">
                            <i class="iconfont icon-list-alt"></i>
                            搜索结果
                        </a>
                    </div>
                    <div class="margin-top" data-search-result>
                        @include('module::CmsWriter.View.pc.part.topicsPage',['topics'=>$topics])
                        <div class="ub-page">
                            {!! $pageHtml !!}
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

@endsection





