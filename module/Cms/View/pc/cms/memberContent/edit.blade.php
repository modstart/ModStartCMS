@extends($_viewMemberFrame)

@section('pageTitleMain')发布内容@endsection

@section('memberBodyContent')

    <div class="ub-panel">
        <div class="head">
            <div class="title">
                <a href="{{modstart_web_url('cms_member_content/edit')}}">选择栏目</a>
                <i class="iconfont icon-angle-right ub-text-muted"></i>
                <span>发布内容</span>
            </div>
        </div>
        <div class="body" style="padding:1rem;">
            <form action="{{\ModStart\Core\Input\Request::currentPageUrl()}}" method="post" data-ajax-form>
                <div class="ub-form vertical">
                    <div class="line">
                        <div class="label">
                            <span>*</span>
                            栏目：
                        </div>
                        <div class="field">
                            {{$cat['title']}}
                            <a href="?" data-tip-popover="重新选择栏目">
                                <i class="iconfont icon-refresh"></i>
                            </a>
                        </div>
                    </div>
                    @if(in_array($model['mode'],[\Module\Cms\Type\CmsMode::LIST_DETAIL, \Module\Cms\Type\CmsMode::PAGE]))
                        <div class="line">
                            <div class="label">
                                <span>*</span>
                                标题：
                            </div>
                            <div class="title">
                                <input type="text" name="title" value="{{$record['title'] or ''}}" class="tw-w-full"/>
                            </div>
                        </div>
                        <div>
                            <?php
                            $f = new \ModStart\Field\Image('cover', ['缩略图']);
                            $f->required()->renderMode(\ModStart\Field\Type\FieldRenderMode::FORM);
                            $f->value($record?$record['cover']:null);
                            echo $f->render();
                            ?>
                        </div>
                        <div>
                            <?php
                            $f = new \ModStart\Field\Tags('tags', ['标签']);
                            $f->required()->serializeType(\ModStart\Field\Tags::SERIALIZE_TYPE_COLON_SEPARATED);
                            $f->renderMode(\ModStart\Field\Type\FieldRenderMode::FORM);
                            $f->value(\ModStart\Core\Util\TagUtil::string2Array($record?$record['tags']:''));
                            echo $f->render();
                            ?>
                        </div>
                    @endif
                    @foreach($model['_customFields'] as $customField)
                        @if($customField['fieldType']==\Module\Cms\Type\CmsModelFieldType::TEXT)
                            <div class="line">
                                <div class="label">
                                    @if($customField['isRequired'])
                                        <span>*</span>
                                    @endif
                                    {{$customField['title']}}：
                                </div>
                                <div class="field">
                                    <input class="form" type="text" name="{{$customField['name']}}" value="{{$recordData?$recordData[$customField['name']]:''}}"/>
                                </div>
                            </div>
                        @elseif($customField['fieldType']==\Module\Cms\Type\CmsModelFieldType::TEXTAREA)
                            <div class="line">
                                <div class="label">
                                    @if($customField['isRequired'])
                                        <span>*</span>
                                    @endif
                                    {{$customField['title']}}：
                                </div>
                                <div class="field">
                                        <textarea class="form" style="height:3rem;"
                                                  name="{{$customField['name']}}">{{$recordData?$recordData[$customField['name']]:''}}</textarea>
                                </div>
                            </div>
                        @elseif($customField['fieldType']==\Module\Cms\Type\CmsModelFieldType::RADIO)
                            <div class="line">
                                <div class="label">
                                    @if($customField['isRequired'])
                                        <span>*</span>
                                    @endif
                                    {{$customField['title']}}：
                                </div>
                                <div class="field">
                                    @if(!empty($customField['fieldData']['options']))
                                        @foreach($customField['fieldData']['options'] as $option)
                                            <label>
                                                <input type="radio" name="{{$customField['name']}}"
                                                       {{$recordData&&$recordData[$customField['name']]==$option?'checked':''}}
                                                       value="{{$option}}"/>
                                                {{$option}}
                                            </label>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        @elseif($customField['fieldType']==\Module\Cms\Type\CmsModelFieldType::SELECT)
                            <div class="line">
                                <div class="label">
                                    @if($customField['isRequired'])
                                        <span>*</span>
                                    @endif
                                    {{$customField['title']}}：
                                </div>
                                <div class="field">
                                    <select name="{{$customField['name']}}">
                                        @foreach($customField['fieldData']['options'] as $option)
                                            <option value="{{$option}}" {{$recordData&&$recordData[$customField['name']]==$option?'selected':''}}>
                                                {{$option}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @elseif($customField['fieldType']==\Module\Cms\Type\CmsModelFieldType::CHECKBOX)
                            <div class="line">
                                <div class="label">
                                    @if($customField['isRequired'])
                                        <span>*</span>
                                    @endif
                                    {{$customField['title']}}：
                                </div>
                                <div class="field">
                                    <?php $values = json_decode($recordData?$recordData[$customField['name']]:'[]',true); ?>
                                    @foreach($customField['fieldData']['options'] as $option)
                                        <label>
                                            <input type="checkbox" name="{{$customField['name']}}[]"
                                                   {{in_array($option,$values)?'checked':''}}
                                                   value="{{$option}}"/>
                                            {{$option}}
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @elseif($customField['fieldType']==\Module\Cms\Type\CmsModelFieldType::IMAGE)
                            <div>
                                <?php
                                $f = new \ModStart\Field\Image($customField['name'], [$customField['title']]);
                                if ($customField['isRequired']) {
                                    $f->required();
                                }
                                $f->renderMode(\ModStart\Field\Type\FieldRenderMode::FORM);
                                $f->value($recordData?$recordData[$customField['name']]:null);
                                echo $f->render();
                                ?>
                            </div>
                        @elseif($customField['fieldType']==\Module\Cms\Type\CmsModelFieldType::FILE)
                            <div>
                                <?php
                                $f = new \ModStart\Field\File($customField['name'], [$customField['title']]);
                                if ($customField['isRequired']) {
                                    $f->required();
                                }
                                $f->renderMode(\ModStart\Field\Type\FieldRenderMode::FORM);
                                $f->value($recordData?$recordData[$customField['name']]:null);
                                echo $f->render();
                                ?>
                            </div>
                        @elseif($customField['fieldType']==\Module\Cms\Type\CmsModelFieldType::DATE)
                            <div class="line">
                                <div class="label">
                                    @if($customField['isRequired'])
                                        <span>*</span>
                                    @endif
                                    {{$customField['title']}}：
                                </div>
                                <div class="field">
                                    <input type="text"
                                           class="form"
                                           style="width:12em;"
                                           value="{{$recordData[$customField['name']] or ''}}"
                                           name="{{$customField['name']}}"
                                           id="{{$customField['name']}}Input"
                                           autocomplete="off"/>
                                    <script>
                                        layui.use('laydate', function () {
                                            var laydate = layui.laydate;
                                            laydate.render({
                                                elem: '#{{$customField['name']}}Input'
                                            });
                                        });
                                    </script>
                                </div>
                            </div>
                        @elseif($customField['fieldType']==\Module\Cms\Type\CmsModelFieldType::DATETIME)
                            <div class="line">
                                <div class="label">
                                    @if($customField['isRequired'])
                                        <span>*</span>
                                    @endif
                                    {{$customField['title']}}：
                                </div>
                                <div class="field">
                                    <input type="text"
                                           class="form"
                                           style="width:12em;"
                                           name="{{$customField['name']}}"
                                           value="{{$recordData[$customField['name']] or ''}}"
                                           id="{{$customField['name']}}Input"
                                           autocomplete="off"/>
                                    <script>
                                        layui.use('laydate', function () {
                                            var laydate = layui.laydate;
                                            laydate.render({
                                                elem: '#{{$customField['name']}}Input',
                                                type: 'datetime'
                                            });
                                        });
                                    </script>
                                </div>
                            </div>
                        @elseif($customField['fieldType']==\Module\Cms\Type\CmsModelFieldType::RICH_TEXT)
                            <div class="line">
                                <div class="label">
                                    @if($customField['isRequired'])
                                        <span>*</span>
                                    @endif
                                    {{$customField['title']}}：
                                </div>
                                <div class="field">
                                    <script type="text/plain" id="{{$customField['name']}}"
                                            name="{{$customField['name']}}">{!! $recordData[$customField['name']] or '' !!}</script>
                                    {!! \ModStart\ModStart::js('asset/common/editor.js') !!}
                                    <script>
                                        $(function () {
                                            window.api.editor.basic('{{$customField['name']}}', {
                                                server: "{{modstart_web_url('member_data/ueditor')}}",
                                                ready: function () {
                                                    // console.log('ready');
                                                }
                                            }, {topOffset: 0});
                                        });
                                    </script>
                                </div>
                            </div>
                        @else
                            <div class="line">
                                <div class="label">
                                    @if($customField['isRequired'])
                                        <span>*</span>
                                    @endif
                                    {{$customField['title']}}：
                                </div>
                                <div class="field">
                                    <pre>{{json_encode($customField,JSON_PRETTY_PRINT)}}</pre>
                                </div>
                            </div>
                        @endif
                    @endforeach
                    <div class="line">
                        <div class="label">
                            <span>*</span>
                            详情：
                        </div>
                        <div class="field">
                            <script type="text/plain" id="content" name="content">{!! $recordData['content'] or '' !!}</script>
                        </div>
                    </div>
                    {!! \ModStart\ModStart::js('asset/common/editor.js') !!}
                    <script>
                        $(function () {
                            window.api.editor.basic('content', {
                                server: "{{modstart_web_url('member_data/ueditor')}}",
                                ready: function () {
                                    // console.log('ready');
                                }
                            }, {topOffset: 0});
                        });
                    </script>
                    <div class="line">
                        <div class="field">
                            <button class="btn btn-primary" type="submit">提交</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection