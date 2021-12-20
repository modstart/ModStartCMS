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
                                    @elseif($customField['fieldType']==\Module\Cms\Type\CmsModelFieldType::TEXTAREA)
                                        <textarea class="form" style="height:3rem;" name="{{$customField['name']}}"></textarea>
                                    @elseif($customField['fieldType']==\Module\Cms\Type\CmsModelFieldType::RADIO)
                                        @if(!empty($customField['fieldData']['options']))
                                            @foreach($customField['fieldData']['options'] as $option)
                                                <label>
                                                    <input type="radio" name="{{$customField['name']}}" value="{{$option}}" />
                                                    {{$option}}
                                                </label>
                                            @endforeach
                                        @endif
                                    @elseif($customField['fieldType']==\Module\Cms\Type\CmsModelFieldType::RADIO)
                                        @foreach($customField['fieldData']['options'] as $option)
                                            <label>
                                                <input type="radio" name="{{$customField['name']}}" value="{{$option}}" />
                                                {{$option}}
                                            </label>
                                        @endforeach
                                    @elseif($customField['fieldType']==\Module\Cms\Type\CmsModelFieldType::SELECT)
                                        <select name="{{$customField['name']}}">
                                            @foreach($customField['fieldData']['options'] as $option)
                                                <option value="{{$option}}">
                                                    {{$option}}
                                                </option>
                                            @endforeach
                                        </select>
                                    @elseif($customField['fieldType']==\Module\Cms\Type\CmsModelFieldType::CHECKBOX)
                                        @foreach($customField['fieldData']['options'] as $option)
                                            <label>
                                                <input type="checkbox" name="{{$customField['name']}}" value="{{$option}}" />
                                                {{$option}}
                                            </label>
                                        @endforeach
                                    @elseif($customField['fieldType']==\Module\Cms\Type\CmsModelFieldType::IMAGE)
                                        <div class="ub-text-muted">暂不支持</div>
                                    @elseif($customField['fieldType']==\Module\Cms\Type\CmsModelFieldType::FILE)
                                        <div class="ub-text-muted">暂不支持</div>
                                    @elseif($customField['fieldType']==\Module\Cms\Type\CmsModelFieldType::DATE)
                                        <input type="text"
                                               class="form"
                                               style="width:12em;"
                                               name="{{$customField['name']}}"
                                               id="{{$customField['name']}}Input"
                                               autocomplete="off" />
                                        <script>
                                            layui.use('laydate', function () {
                                                var laydate = layui.laydate;
                                                laydate.render({
                                                    elem: '#{{$customField['name']}}Input'
                                                });
                                            });
                                        </script>
                                    @elseif($customField['fieldType']==\Module\Cms\Type\CmsModelFieldType::DATETIME)
                                        <input type="text"
                                               class="form"
                                               style="width:12em;"
                                               name="{{$customField['name']}}"
                                               id="{{$customField['name']}}Input"
                                               autocomplete="off" />
                                        <script>
                                            layui.use('laydate', function () {
                                                var laydate = layui.laydate;
                                                laydate.render({
                                                    elem: '#{{$customField['name']}}Input',
                                                    type: 'datetime'
                                                });
                                            });
                                        </script>
                                    @elseif($customField['fieldType']==\Module\Cms\Type\CmsModelFieldType::RICH_TEXT)
                                        <div class="ub-text-muted">暂不支持</div>
                                    @else
                                        <pre>{{json_encode($customField,JSON_PRETTY_PRINT)}}</pre>
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





