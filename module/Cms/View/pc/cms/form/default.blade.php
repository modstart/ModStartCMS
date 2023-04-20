@extends($_viewFrame)

@section('pageTitleMain'){{$cat['seoTitle']?$cat['seoTitle']:$cat['title']}}@endsection
@section('pageKeywords'){{$cat['seoKeywords']?$cat['seoKeywords']:$cat['title']}}@endsection
@section('pageDescription'){{$cat['seoDescription']?$cat['seoDescription']:$cat['title']}}@endsection

@section('bodyContent')

    <div class="ub-content">
        <div class="panel-a"
             @if($cat['bannerBg'])
             style="background-image:url({{\ModStart\Core\Assets\AssetsUtil::fix($cat['bannerBg'])}});"
             @else
             style="background-image:var(--color-primary-gradient-bg);"
            @endif
        >
            <div class="box">
                <h1 class="title animated fadeInUp">
                    {{$cat['title']}}
                </h1>
                <div class="sub-title animated fadeInUp">
                    {{$cat['subTitle']}}
                </div>
            </div>
        </div>
    </div>

    <div class="ub-container">
        <div class="ub-breadcrumb">
            <a href="{{modstart_web_url('')}}">首页</a>
            @foreach($catChain as $i=>$c)
                <a href="{{modstart_web_url($c['url'])}}">{{$c['title']}}</a>
            @endforeach
        </div>
    </div>

    <div class="ub-container margin-bottom">
        <div class="ub-content-bg tw-p-3 tw-rounded-lg">
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
                                    <?php $f = \Module\Cms\Field\CmsField::getByNameOrFail($customField['fieldType']); ?>
                                    {!! $f->renderForUserInput($customField) !!}
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





