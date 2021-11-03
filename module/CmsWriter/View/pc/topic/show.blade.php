@extends($_viewFrame)

@section('pageTitleMain'){{$topic['title']}}@endsection

{!! \ModStart\ModStart::styleFile('public/vendor/CmsWriter/asset/notesns.css') !!}
{!! \ModStart\ModStart::scriptFile('public/vendor/CmsWriter/asset/notesns.js') !!}
{!! \ModStart\ModStart::styleFile('public/vendor/MemberFollow/asset/memberFollow.css') !!}
{!! \ModStart\ModStart::scriptFile('public/vendor/MemberFollow/asset/memberFollow.js') !!}

@section('bodyContent')

    <div class="ub-container">

        <div class="row">
            <div class="col-md-9">

                <div class="margin-top tw-bg-white tw-p-4 tw-rounded tw-mb-2" >
                    <div class="tw-flex tw-justify-between tw-items-center">
                        <div class="tw-flex">
                            <div class="tw-mr-4">
                                <div class="ub-cover-1-1 tw-shadow tw-w-20 tw-h-20 tw-rounded"
                                     style="background-image:url({{\ModStart\Core\Assets\AssetsUtil::fix($topic['cover'])}});">
                                </div>
                            </div>
                            <div class="tw-py-3">
                                <div class="tw-font-bold tw-text-xl tw-text-gray-700">{{$topic['title']}}</div>
                                <div class="tw-text-gray-400 tw-text-sm tw-mt-2">
                                    收录了{{intval($topic['noteCount'])}}篇文章 · {{intval($topic['followerCount'])}}人关注
                                </div>
                            </div>
                        </div>
                        <div data-topic-follow-item data-status="{{$topic['_isFollow']?'is_follow':'not_follow'}}" data-alias="{{$topic['alias']}}">
                            @if($topic['pushEnable'])
                                @if(\Module\Member\Auth\MemberUser::id()==$topic['memberUserId'])
                                    <a href="javascript:;" data-dialog-request="{{modstart_web_url('topic/note_add',['topicAlias'=>$topic['alias']])}}" class="btn btn-lg btn-primary-line btn-round">
                                        <i class="iconfont icon-plus"></i>
                                        添加文章
                                    </a>
                                @else
                                    @if(\Module\Member\Auth\MemberUser::isLogin())
                                        <a href="javascript:;" data-dialog-request="{{modstart_web_url('topic/note_apply',['topicAlias'=>$topic['alias']])}}" class="btn btn-lg btn-primary-line btn-round">
                                            <i class="iconfont icon-fly"></i>
                                            投稿
                                        </a>
                                    @else
                                        <a href="{{modstart_web_url('login',['redirect'=>modstart_web_url('t/'.$topic['alias'])])}}" class="btn btn-lg btn-primary-line btn-round">
                                            <i class="iconfont icon-fly"></i>
                                            投稿
                                        </a>
                                    @endif
                                @endif
                            @endif
                            <a href="javascript:;" data-action="follow" class="btn btn-lg btn-primary btn-round">
                                <i class="iconfont icon-plus"></i>
                                关注
                            </a>
                            <a href="javascript:;" data-action="unfollow" class="btn btn-lg btn-round">
                                <i class="iconfont icon-check"></i>
                                已关注
                            </a>
                        </div>
                    </div>
                </div>

                <div class="tw-bg-white tw-rounded">
                    <div class="ub-nav-tab margin-top">
                        <a href="{{modstart_web_url('note')}}" class="active">
                            <i class="iconfont icon-list-alt"></i>
                            文章列表
                        </a>
                    </div>
                    <div class="margin-top">
                        @include('module::CmsWriter.View.pc.part.notes',['notes'=>$notes])
                    </div>
                </div>
                <div class="ub-page">
                    {!! $pageHtml !!}
                </div>
            </div>
            <div class="col-md-3">

                <div class="ub-panel margin-top">
                    <div class="head">
                        <div class="title">{{$topic['title']}}</div>
                    </div>
                    <div class="body">
                        <div class="ub-html">
                            {!! \ModStart\Core\Util\HtmlUtil::text2html($topic['description']) !!}
                        </div>
                    </div>
                </div>
                @if($topic['memberUserId']===\Module\Member\Auth\MemberUser::id())
                    <div class="ub-panel">
                        <div class="head">
                            <div class="title">管理</div>
                        </div>
                        <div class="body">
                            <div>
                                <a class="btn btn-block" data-dialog-request="{{modstart_web_url('topic/note_apply_verify',['topicAlias'=>$topic['alias']])}}" href="javascript:;">审核投稿</a>
                            </div>
                            <div class="margin-top">
                                <a class="btn btn-block" href="{{modstart_web_url('topic/edit',['_id'=>$topic['id']])}}">编辑</a>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="ub-panel">
                    <div class="head">
                        <div class="title">分享</div>
                    </div>
                    <div class="body">
                        <div>
                            @include('module::Vendor.View.public.shareButtons')
                        </div>
                    </div>
                </div>
                <div class="ub-panel">
                    <div class="head">
                        <div class="title">管理员</div>
                    </div>
                    <div class="body">
                        <div class="tw-py-1 tw-flex tw-justify-between tw-items-center">
                            <div class="tw-flex">
                                <div class="tw-mr-4">
                                    <a href="{{modstart_web_url('note_member/'.$topicOwner['id'])}}" class="ub-cover-1-1 tw-shadow tw-w-10 tw-h-10 tw-rounded-full"
                                         style="background-image:url({{\ModStart\Core\Assets\AssetsUtil::fix($topicOwner['avatar'])}});">
                                    </a>
                                </div>
                                <div>
                                    <a href="{{modstart_web_url('note_member/'.$topicOwner['id'])}}" class="tw-font-bold tw-text-gray-700">{{$topicOwner['username']}}</a>
                                    <div class="tw-text-gray-400 tw-text-sm">{{$topicOwner['signature']}}</div>
                                </div>
                            </div>
                            @if($topicOwner['id']!=\Module\Member\Auth\MemberUser::id())
                                <div data-member-follow-item data-status="{{$topicOwner['_isFollow']?'is_follow':'not_follow'}}" data-id="{{$topicOwner['id']}}">
                                    <a href="javascript:;" data-action="follow" class="btn btn-primary-line btn-round">
                                        <i class="iconfont icon-plus"></i>
                                        关注
                                    </a>
                                    <a href="javascript:;" data-action="unfollow" class="btn btn-round">
                                        <i class="iconfont icon-check"></i>
                                        已关注
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="ub-panel">
                    <div class="head">
                        <div class="title">关注的人</div>
                    </div>
                    <div class="body">
                        @include('module::CmsWriter.View.pc.part.users',['users'=>$followers])
                    </div>
                </div>

            </div>
        </div>

    </div>

@endsection





