@extends($_viewFrame)

@section('pageTitleMain'){{$post['title']}}@endsection

{!! \ModStart\ModStart::styleFile('public/vendor/Cms/asset/cms.css') !!}
{!! \ModStart\ModStart::scriptFile('public/vendor/Cms/asset/cms.js') !!}

@section('bodyContent')

    <div class="ub-container" style="max-width:35rem;background:#FFF;">

        <div class="ub-article margin-top white tw-rounded">

{{--            @if($post['status']!==\Module\Cms\Type\NoteStatus::VERIFY_PASS)--}}
{{--                <div class="ub-alert ub-alert-danger ub-text-center">当前文章正在审核</div>--}}
{{--            @endif--}}

            <h1>{{$post['title']}}</h1>
            <div class="attr">
                <div>
                    @if($post['isOriginal'])
                        <span class="ub-tag primary">原创</span>
                    @endif
                    @if(!empty($post['tags']))
                        @foreach($post['tags'] as $tag)
                            <span class="ub-tag">{{$tag}}</span>
                        @endforeach
                    @endif
                    更新：{{$post['created_at']}}
                </div>
            </div>
            <div class="content ub-html">
                {!! $post['contentHtml'] !!}
            </div>
        </div>

        @if(0)
        <div class="ub-text-right tw-py-4">
            <a class="tw-text-gray-400" href="javascript:;"
               data-dialog-request="{{\Module\ContentReport\Util\ContentReportUtil::submitPageUrl('post',$post['id'],$post['title'])}}">
                举报
            </a>
            <span class="tw-text-gray-400 tw-ml-2">
                &copy; 著作权归作者所有
            </span>
        </div>
        @endif

        <div class="margin-top ub-padding">
            <div class="tw-text-center">
                @include('module::Vendor.View.public.shareButtons')
            </div>

            @if(0)
            <div>
                <div class="row">
                    <div class="col-md-6">
                        @if(0)
                        <div class="tw-inline-block" data-post-like-item data-alias="{{$post['alias']}}" data-status="{{$post['_isLike']?'is_like':'not_like'}}">
                            <a class="btn btn-primary btn-round" data-action="like" href="javascript:;">
                                <i class="iconfont icon-xiangqu"></i>
                                喜欢
                                |
                                <span class="cnt">{{$post['likeCount'] or 0}}</span>
                            </a>
                            <a class="btn btn-round" data-action="unlike" href="javascript:;">
                                <i class="iconfont icon-xiangqu"></i>
                                已喜欢
                                |
                                <span class="cnt">{{$post['likeCount'] or 0}}</span>
                            </a>
                        </div>
                        @endif
                    </div>
                    <div class="col-md-6 ub-text-right">

                    </div>
                </div>
            </div>
            @endif

            @if(!empty($post['_memberUser']))
                <div class="margin-top-lg tw-bg-gray-100 tw-p-4 tw-rounded tw-mb-2">
                    <div class="tw-flex tw-justify-between tw-items-center">
                        <div class="tw-flex">
                            <div class="tw-mr-4">
                                <div class="ub-cover-1-1 tw-shadow tw-w-10 tw-h-10 tw-rounded-full"
                                     style="background-image:url({{\ModStart\Core\Assets\AssetsUtil::fix($post['_memberUser']['avatar'])}});">
                                </div>
                            </div>
                            <div>
                                <div class="tw-font-bold tw-text-gray-700">{{$post['_memberUser']['username'] or '[无用户]'}}</div>
                                <div class="tw-text-gray-400 tw-text-sm">
                                    写了 {{$post['_memberUser']['cmsWordCount'] or 0}} 字
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tw-border-0 tw-border-solid tw-border-t tw-border-gray-200 tw-mt-2 tw-pt-2 tw-text-gray-500">
                        {{$post['_memberUser']['signature'] or 'Ta没有介绍内容'}}
                    </div>
                </div>
            @endif

        </div>

    </div>

    <div class="ub-container margin-top" style="max-width:35rem;">
        <div>
            @include('module::MemberComment.View.pc.public.comment',['biz'=>'post','bizId'=>$post['id']])
        </div>
    </div>

@endsection





