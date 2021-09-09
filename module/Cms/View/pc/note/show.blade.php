@extends($_viewFrame)

@section('pageTitleMain'){{$note['title']}}@endsection

{!! \ModStart\ModStart::styleFile('public/vendor/Cms/asset/notesns.css') !!}
{!! \ModStart\ModStart::scriptFile('public/vendor/Cms/asset/notesns.js') !!}
{!! \ModStart\ModStart::styleFile('public/vendor/MemberFollow/asset/memberFollow.css') !!}
{!! \ModStart\ModStart::scriptFile('public/vendor/MemberFollow/asset/memberFollow.js') !!}
@section('bodyContent')

    <div class="ub-container" style="max-width:35rem;background:#FFF;">

        <div class="ub-article margin-top white tw-rounded">

            @if($note['status']!==\Module\Cms\Type\NoteStatus::VERIFY_PASS)
                <div class="ub-alert ub-alert-danger ub-text-center">当前文章正在审核</div>
            @endif

            <h1>{{$note['title']}}</h1>
            <div class="attr">
                <div>
                    @if($note['isOriginal'])
                        <span class="ub-tag primary">原创</span>
                    @endif
                    @if(!empty($note['tags']))
                        @foreach($note['tags'] as $tag)
                            <span class="ub-tag">{{$tag}}</span>
                        @endforeach
                    @endif
                    更新：{{$note['created_at']}}
                </div>
            </div>
            <div class="content ub-html">
                {!! $note['contentHtml'] !!}
            </div>
        </div>

        <div class="ub-text-right tw-py-4">
            <a class="tw-text-gray-400" href="javascript:;"
               data-dialog-request="{{\Module\ContentReport\Util\ContentReportUtil::submitPageUrl('note',$note['id'],$note['title'])}}">
                举报
            </a>
            <span class="tw-text-gray-400 tw-ml-2">
                &copy; 著作权归作者所有
            </span>
        </div>

        <div class="margin-top ub-padding">
            <div>
                <div class="row">
                    <div class="col-md-6">

                        <div class="tw-inline-block" data-note-like-item data-alias="{{$note['alias']}}" data-status="{{$note['_isLike']?'is_like':'not_like'}}">
                            <a class="btn btn-primary btn-round" data-action="like" href="javascript:;">
                                <i class="iconfont icon-xiangqu"></i>
                                喜欢
                                |
                                <span class="cnt">{{$note['likeCount']}}</span>
                            </a>
                            <a class="btn btn-round" data-action="unlike" href="javascript:;">
                                <i class="iconfont icon-xiangqu"></i>
                                已喜欢
                                |
                                <span class="cnt">{{$note['likeCount']}}</span>
                            </a>
                        </div>
                        <a class="btn btn-round" href="javascript:;" data-dialog-request="{{modstart_web_url('n/'.$note['alias'].'/add_to_topic')}}">
                            <i class="iconfont icon-plus"></i>
                            收录到我的专题
                            |
                            <span>{{$note['viewCount']}}</span>
                        </a>
                    </div>
                    <div class="col-md-6 ub-text-right">
                        @include('module::Vendor.View.public.shareButtons')
                    </div>
                </div>
            </div>

            @include('module::MemberReward.View.pc.public.reward',['memberUserId'=>$note['memberUserId'],'biz'=>'note','bizId'=>$note['id']])

            <div class="margin-top-lg tw-bg-gray-100 tw-p-4 tw-rounded tw-mb-2">
                <div class="tw-flex tw-justify-between tw-items-center">
                    <div class="tw-flex">
                        <div class="tw-mr-4">
                            <div class="ub-cover-1-1 tw-shadow tw-w-10 tw-h-10 tw-rounded-full"
                                 style="background-image:url({{\ModStart\Core\Assets\AssetsUtil::fix($note['_memberUser']['avatar'])}});">
                            </div>
                        </div>
                        <div>
                            <div class="tw-font-bold tw-text-gray-700">{{$note['_memberUser']['username']}}</div>
                            <div class="tw-text-gray-400 tw-text-sm">
                                写了 {{$note['_memberUser']['wordCount']}} 字，被 {{$note['_memberUser']['followerCount']}} 人关注
                            </div>
                        </div>
                    </div>
                    <div>
                        @if($note['_memberUser']['id']!=\Module\Member\Auth\MemberUser::id())
                            <div data-member-follow-item data-status="{{$note['_memberUser']['_isFollow']?'is_follow':'not_follow'}}" data-id="{{$note['_memberUser']['id']}}">
                                <a href="javascript:;" data-action="follow" class="btn btn-primary btn-round">
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
                <div class="tw-border-0 tw-border-solid tw-border-t tw-border-gray-200 tw-mt-2 tw-pt-2 tw-text-gray-500">
                    {{$note['_memberUser']['signature']}}
                </div>
            </div>

        </div>

    </div>

    @if(modstart_config('MemberComment_Enable',false))
        <div class="ub-container margin-top" style="max-width:35rem;">
            <div>
                @include('module::MemberComment.View.pc.public.comment',['biz'=>'note','bizId'=>$note['id']])
            </div>
        </div>
    @endif

@endsection





