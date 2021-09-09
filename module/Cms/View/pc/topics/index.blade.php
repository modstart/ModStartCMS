@extends($_viewFrame)

@section('pageTitleMain','文章')

{!! \ModStart\ModStart::styleFile('public/vendor/Cms/asset/notesns.css') !!}
{!! \ModStart\ModStart::scriptFile('public/vendor/Cms/asset/notesns.js') !!}
{!! \ModStart\ModStart::styleFile('public/vendor/MemberFollow/asset/memberFollow.css') !!}
{!! \ModStart\ModStart::scriptFile('public/vendor/MemberFollow/asset/memberFollow.js') !!}
@section('bodyContent')

    <div class="ub-container">

        <div class="row">
            <div class="col-md-3">

                <div class="margin-top">
                    <div class="ub-menu simple">
                        <a class="title {{modstart_baseurl_active(modstart_web_url('topics'))}}" href="{{modstart_web_url('topics')}}">
                            <i class="iconfont icon-category"></i>
                            推荐专题
                        </a>
                        <a class="title {{modstart_baseurl_active(modstart_web_url('topics/latest'))}}" href="{{modstart_web_url('topics/latest')}}">
                            <i class="iconfont icon-category"></i>
                            最新专题
                        </a>
                    </div>
                </div>

                <div class="margin-top ub-padding">
                    <a class="btn btn-lg btn-primary-line btn-round btn-block" href="{{modstart_web_url('topic/edit')}}">
                        <i class="iconfont icon-edit"></i>
                        创建我的专题
                    </a>
                </div>

                <div class="ub-panel margin-top">
                    <div class="head">
                        <div class="title">推荐用户</div>
                    </div>
                    <div class="body tw-bg-white">
                        @include('module::Cms.View.pc.part.users',['users'=>$recommendUsers])
                    </div>
                </div>

            </div>
            <div class="col-md-9">
                <div class="ub-panel margin-top" style="background:transparent;">
                    <div class="head">
                        <div class="title">最新专题</div>
                    </div>
                    <div class="body">
                        @include('module::Cms.View.pc.part.topicsPage',['topics'=>$topics])
                        <div class="ub-page">
                            {!! $pageHtml !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection





