@extends($_viewFrame)

@section('pageTitleMain','关注的专题文章')

{!! \ModStart\ModStart::styleFile('public/vendor/CmsWriter/asset/notesns.css') !!}
{!! \ModStart\ModStart::scriptFile('public/vendor/CmsWriter/asset/notesns.js') !!}
{!! \ModStart\ModStart::styleFile('public/vendor/MemberFollow/asset/memberFollow.css') !!}
{!! \ModStart\ModStart::scriptFile('public/vendor/MemberFollow/asset/memberFollow.js') !!}
@section('bodyContent')

    <div class="ub-container">

        <div class="row">
            <div class="col-md-3">

                <div class="margin-top">
                    <div class="ub-menu simple">
                        <a class="title {{modstart_baseurl_active(modstart_web_url('follow/topics'))}}" href="{{modstart_web_url('follow/topics')}}">
                            <i class="iconfont icon-category"></i>
                            关注的专题
                        </a>
                        <a class="title {{modstart_baseurl_active(modstart_web_url('follow/users'))}}" href="{{modstart_web_url('follow/users')}}">
                            <i class="iconfont icon-user"></i>
                            关注的用户
                        </a>
                    </div>
                </div>

                <div class="ub-panel margin-top">
                    <div class="head">
                        <div class="title">推荐专题</div>
                    </div>
                    <div class="body tw-bg-white">
                        @include('module::CmsWriter.View.pc.part.topics',['topics'=>$recommendTopics])
                    </div>
                </div>

            </div>
            <div class="col-md-9">

                <div class="tw-bg-white tw-rounded">
                    <div class="ub-nav-tab margin-top">
                        <a href="javascript:;" class="active">
                            <i class="iconfont icon-list-alt"></i>
                            关注的专题文章
                        </a>
                    </div>
                    <div class="margin-top">
                        @include('module::CmsWriter.View.pc.part.notes',['notes'=>$notes])
                        <div class="ub-page">
                            {!! $pageHtml !!}
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

@endsection





