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
                                <input type="text" name="title" value="{{empty($record['title'])?'':$record['title']}}" class="tw-w-full"/>
                            </div>
                        </div>
                        <div>
                            <?php
                            $f = new \ModStart\Field\Image('cover', ['缩略图']);
                            $f->server(modstart_web_url('member_data/file_manager/image'));
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
                        <?php $f = \Module\Cms\Field\CmsField::getByNameOrFail($customField['fieldType']); ?>
                        <div class="line">
                            <div class="label">
                                @if($customField['isRequired'])
                                    <span>*</span>
                                @endif
                                {{$customField['title']}}：
                            </div>
                            <div class="field">
                                {!! $f->renderForUserInput($customField,$recordData) !!}
                            </div>
                        </div>
                    @endforeach
                    <div class="line">
                        <div class="label">
                            <span>*</span>
                            详情：
                        </div>
                        <div class="field">
                            <script type="text/plain" id="content" name="content">{!! empty($recordData['content'])?'':$recordData['content'] !!}</script>
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
