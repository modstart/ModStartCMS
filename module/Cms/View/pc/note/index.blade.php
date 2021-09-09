@extends($_viewFrame)

@section('pageTitleMain')文章@endsection

{!! \ModStart\ModStart::styleFile('public/vendor/Cms/asset/notesns.css') !!}
{!! \ModStart\ModStart::scriptFile('public/vendor/Cms/asset/notesns.js') !!}
{!! \ModStart\ModStart::styleFile('public/vendor/MemberFollow/asset/memberFollow.css') !!}
{!! \ModStart\ModStart::scriptFile('public/vendor/MemberFollow/asset/memberFollow.js') !!}
@section('bodyContent')

    <div class="ub-container">

        <div class="row">
            <div class="col-md-9">
                <div class="tw-bg-white tw-rounded">
                    <div class="ub-nav-tab margin-top">
                        <a href="{{modstart_web_url('note')}}" class="{{modstart_baseurl_active(modstart_web_url('note'))}}">
                            <i class="iconfont icon-list-alt"></i>
                            推荐文章
                        </a>
                        <a href="{{modstart_web_url('note/comments')}}" class="{{modstart_baseurl_active(modstart_web_url('note/comments'))}}">
                            <i class="iconfont icon-pinglun"></i>
                            热论文章
                        </a>
                        <a href="{{modstart_web_url('note/views')}}" class="{{modstart_baseurl_active(modstart_web_url('note/views'))}}">
                            <i class="iconfont icon-star"></i>
                            最热文章
                        </a>
                        <a href="{{modstart_web_url('note/latest')}}" class="{{modstart_baseurl_active(modstart_web_url('note/latest'))}}">
                            <i class="iconfont icon-details"></i>
                            最新文章
                        </a>
                    </div>
                    <div class="margin-top">
                        @include('module::Cms.View.pc.part.notes',['notes'=>$notes])
                    </div>
                    <div class="ub-page">
                        {!! $pageHtml !!}
                    </div>
                </div>
            </div>
            <div class="col-md-3">

                <div class="margin-top">
                    <a class="btn btn-lg btn-primary btn-block" href="{{modstart_web_url('writer')}}">
                        <i class="iconfont icon-edit"></i>
                        开始创作
                    </a>
                </div>

                <div class="ub-panel margin-top">
                    <div class="head">
                        <div class="title">推荐专题</div>
                    </div>
                    <div class="body tw-bg-white">
                        @include('module::Cms.View.pc.part.topics',['topics'=>$recommendTopics])
                    </div>
                </div>

                <div class="ub-panel margin-top" data-user-box>
                    <div class="head">
                        <div class="ub-nav-tab">
                            <a href="javascript:;" class="active" style="padding:0;">
                                推荐用户
                            </a>
                            <a href="javascript:;" style="padding:0;">
                                最新用户
                            </a>
                        </div>
                    </div>
                    <div class="body tw-bg-white">
                        <div data-user-item>
                            @include('module::Cms.View.pc.part.users',['users'=>$recommendUsers])
                        </div>
                        <div data-user-item class="tw-hidden">
                            @include('module::Cms.View.pc.part.users',['users'=>$latestUsers])
                        </div>
                    </div>
                    <script>
                        $(function () {
                            var $box = $('[data-user-box]');
                            var $tab = $box.find('.ub-nav-tab a');
                            $tab.on('click', function () {
                                var index = $(this).index();
                                $box.find('[data-user-item]').addClass('tw-hidden').eq(index).removeClass('tw-hidden');
                                $tab.removeClass('active').eq(index).addClass('active');
                                return false;
                            });
                        });
                    </script>
                </div>

            </div>
        </div>

    </div>

@endsection





