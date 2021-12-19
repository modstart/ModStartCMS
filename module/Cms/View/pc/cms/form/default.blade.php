@extends($_viewFrame)

@section('pageTitleMain'){{$cat['seoTitle']?$cat['seoTitle']:$cat['title']}}@endsection
@section('pageKeywords'){{$cat['seoKeywords']?$cat['seoKeywords']:$cat['title']}}@endsection
@section('pageDescription'){{$cat['seoDescription']?$cat['seoDescription']:$cat['title']}}@endsection

@section('bodyContent')

    <div class="lg:tw-text-left tw-text-center tw-text-white tw-text-lg tw-py-20 tw-bg-gray-500 ub-cover"
         @if($cat['bannerBg']) style="background-image:url({{\ModStart\Core\Assets\AssetsUtil::fix($cat['bannerBg'])}});" @endif
    >
        <div class="ub-container">
            <h1 class="tw-text-4xl animated fadeInUp">{{$cat['title']}}</h1>
            <div class="tw-mt-4 animated fadeInUp">
                {{$cat['subTitle']}}
            </div>
        </div>
    </div>

    <div class="ub-container" style="max-width:800px;">
        <div class="ub-breadcrumb">
            <a href="{{modstart_web_url('')}}">首页</a>
            @foreach($catChain as $i=>$c)
                <a href="{{modstart_web_url($c['url'])}}">{{$c['title']}}</a>
            @endforeach
        </div>
    </div>

    <div class="ub-container" style="max-width:800px;">
        <div class="ub-panel" style="padding:2rem 1rem;">
            <form action="?" method="post" data-ajax-form>
                <div class="ub-form">
                    @if(!empty($cat['_model']['_customFields']))
                        @foreach($cat['_model']['_customFields'] as $customField)
                            <div class="line">
                                <div class="label">
                                    @if($customField['isRequired'])
                                        <span>*</span>
                                    @endif
                                    {{$customField['title']}}：
                                </div>
                                <div class="field">
                                    @if($customField['fieldType']==\Module\Cms\Type\CmsModelFieldType::TEXT)
                                        <input class="form" type="text" name="{{$customField['name']}}" />
                                    @endif
                                    @if($customField['fieldType']==\Module\Cms\Type\CmsModelFieldType::TEXTAREA)
                                        <textarea class="form" style="height:3rem;" name="{{$customField['name']}}"></textarea>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif
                    <div class="line">
                        <div class="label">
                            <span>*</span>
                            内容：
                        </div>
                        <div class="field">
                            <textarea class="form" style="height:3rem;" name="content"></textarea>
                        </div>
                    </div>
                    <div class="line">
                        <div class="label">&nbsp;</div>
                        <div class="field">
                            <button type="submit" class="btn btn-primary">提交</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection





