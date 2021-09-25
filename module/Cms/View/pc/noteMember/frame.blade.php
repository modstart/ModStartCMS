@extends($_viewFrame)

{!! \ModStart\ModStart::styleFile('public/vendor/Cms/asset/notesns.css') !!}
{!! \ModStart\ModStart::scriptFile('public/vendor/Cms/asset/notesns.js') !!}
{!! \ModStart\ModStart::styleFile('public/vendor/MemberFollow/asset/memberFollow.css') !!}
{!! \ModStart\ModStart::scriptFile('public/vendor/MemberFollow/asset/memberFollow.js') !!}
@section('bodyContent')

    <div class="ub-container">

        <div class="row">
            <div class="col-md-9">

                <div class="margin-top tw-bg-white tw-p-4 tw-rounded tw-mb-2">
                    <div class="tw-flex tw-justify-between tw-items-center">
                        <div class="tw-flex">
                            <div class="tw-mr-4">
                                <div class="ub-cover-1-1 tw-shadow tw-w-20 tw-h-20 tw-rounded-full"
                                     style="background-image:url({{\ModStart\Core\Assets\AssetsUtil::fix($user['avatar'])}});"></div>
                            </div>
                            <div class="tw-py-3">
                                <div>
                                    <span class="tw-font-bold tw-text-xl tw-text-gray-700">{{$user['username']}}</span>
                                    @if($user['gender']==\Module\Member\Type\Gender::MALE)
                                        <i class="iconfont icon-male tw-text-blue-500 tw-text-sm"></i>
                                    @endif
                                    @if($user['gender']==\Module\Member\Type\Gender::FEMALE)
                                        <i class="iconfont icon-female tw-text-pink-500 tw-text-sm"></i>
                                    @endif
                                </div>
                                <div class="tw-mt-2">
                                    <div class="tw-flex">
                                        <a class="tw-block" href="{{modstart_web_url('member_follow/'.$user['id'].'/following')}}">
                                            <div class="tw-text-lg tw-text-gray-700">{{intval($user['followCount'])}}</div>
                                            <div class="tw-text-gray-400 tw-text-sm">关注</div>
                                        </a>
                                        <a class="tw-block tw-pl-4" href="{{modstart_web_url('member_follow/'.$user['id'].'/followers')}}">
                                            <div class="tw-text-lg tw-text-gray-700">{{intval($user['followerCount'])}}</div>
                                            <div class="tw-text-gray-400 tw-text-sm">粉丝</div>
                                        </a>
                                        <div class="tw-block tw-pl-4">
                                            <div class="tw-text-lg tw-text-gray-700">{{intval($user['wordCount'])}}</div>
                                            <div class="tw-text-gray-400 tw-text-sm">字数</div>
                                        </div>
                                        <div class="tw-block tw-pl-4">
                                            <div class="tw-text-lg tw-text-gray-700">{{intval($user['noteCount'])}}</div>
                                            <div class="tw-text-gray-400 tw-text-sm">文章</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            @if($user['id']!=\Module\Member\Auth\MemberUser::id())
                                <a href="{{modstart_web_url('member_chat#chat_create/'.$user['id'])}}" class="btn btn-lg btn-primary-line btn-round">
                                    <i class="iconfont icon-pinglun"></i>
                                    发私信
                                </a>
                                <div class="tw-inline-block" data-member-follow-item data-status="{{$user['_isFollow']?'is_follow':'not_follow'}}" data-id="{{$user['id']}}">
                                    <a href="javascript:;" data-action="follow" class="btn btn-lg btn-primary-line btn-round">
                                        <i class="iconfont icon-plus"></i>
                                        关注
                                    </a>
                                    <a href="javascript:;" data-action="unfollow" class="btn btn-lg btn-round">
                                        <i class="iconfont icon-check"></i>
                                        已关注
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="tw-bg-white tw-rounded">
                    @section('noteMemberContent')@show
                </div>
            </div>
            <div class="col-md-3">
                <div class="ub-panel margin-top">
                    <div class="head">
                        <div class="title">个人介绍</div>
                    </div>
                    <div class="body">
                        <div class="ub-html">
                            {!! \ModStart\Core\Util\HtmlUtil::text2html($user['signature']) !!}
                        </div>
                    </div>
                </div>
                <div class="ub-panel">
                    <div class="head"></div>
                    <div class="body">
                        <a href="{{modstart_web_url('note_member/'.$user['id'].'/like_notes')}}"
                           class="btn btn-block {{modstart_baseurl_active(modstart_web_url('note_member/'.$user['id'].'/like_notes'),'ub-text-primary')}}">
                            <i class="iconfont icon-heart-o"></i>
                            喜欢的文章
                        </a>
                        <a href="{{modstart_web_url('note_member/'.$user['id'].'/followed_topics')}}"
                           class="btn btn-block margin-top {{modstart_baseurl_active(modstart_web_url('note_member/'.$user['id'].'/followed_topics'),'ub-text-primary')}}">
                            <i class="iconfont icon-category"></i>
                            关注的专题
                        </a>
                    </div>
                </div>
                <div class="ub-panel">
                    <div class="head">
                        <div class="title">TA创建的专题</div>
                    </div>
                    <div class="body">
                        @include('module::Cms.View.pc.part.topics',['topics'=>$topics])
                    </div>
                </div>

            </div>
        </div>

    </div>

@endsection





