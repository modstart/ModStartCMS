<div class="line" id="{{$id}}">
    <div class="label">
        {!! str_contains($rules,'required')?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        {{$label}}:
    </div>
    <div class="field">
        <input type="hidden"
               {{$readonly?'readonly':''}}
               class="form"
               name="{{$name}}"
               placeholder="{{$placeholder}}"
               value="{{$value}}"/>
        <div id="{{$name}}Selector" class="ub-image-selector @if(in_array($uploadMode,['uploadDirectRaw','uploadDirect'])) raw-mode @endif @if(!empty($value)) has-value @endif">
            <div class="tools">
                <a href="javascript:;" class="action close" data-close><i class="iconfont icon-close"></i></a>
                <a href="javascript:;" class="action preview" data-preview><i class="iconfont icon-eye"></i></a>
                @if(!$uploadMode)
                    <a href="javascript:;" class="action add" data-add><i class="iconfont icon-plus"></i></a>
                @endif
            </div>
            @if(!empty($value))
                <div class="cover ub-cover-1-1 contain" style="background-image:url({{$value}});"></div>
            @else
                <div class="cover ub-cover-1-1 contain"
                     style="background-image:url(@asset('asset/image/none.png'));"></div>
            @endif
            @if(in_array($uploadMode,['uploadDirectRaw','uploadDirect']))
                {!! \ModStart\ModStart::js('asset/common/uploadButton.js') !!}
                <div id="{{$id}}Uploader" class="uploader"></div>
            @endif
        </div>
        <script>
            $(function () {
                var $field = $('#{{$id}}');
                var $selector = $('#{{$name}}Selector');

                function setValue(path) {
                    $field.find('[name="{{$name}}"]').val(path);
                    if (path) {
                        $selector.find('.cover').css('backgroundImage', "url(" + path + ")");
                        $selector.addClass('has-value');
                    } else {
                        $selector.find('.cover').css('backgroundImage', "url(@asset('asset/image/none.png'))");
                        $selector.removeClass('has-value');
                    }
                }

                @if(in_array($uploadMode,['uploadDirectRaw','uploadDirect']))
                window.api.uploadButton('#{{$id}}Uploader', {
                    text: '<div style="padding:0;background:transparent;width:3rem;height:3rem;"><span class="iconfont icon-plus" style="display:inline-block;background:transparent;width:3rem;height:3rem;line-height:3rem;"></span></div>',
                    swf: "@asset('asset/vendor/webuploader/Uploader.swf')",
                    server: "{{$server}}?action={{$uploadMode}}",
                    extensions: window.__dataConfig.category.image.extensions.join(','),
                    sizeLimit: window.__dataConfig.category.image.maxSize,
                    chunkSize: window.__dataConfig.chunkSize,
                    showFileQueue: true,
                    fileNumLimit: 1,
                    callback: function (file, me) {
                        setValue(file.fullPath);
                    },
                    finish: function () {
                    }
                });
                @else
                $selector.find('.tools .add').on('click', function () {
                    window.__selectorDialog = new window.api.selectorDialog({
                        server: '{{$server}}',
                        callback: function (items) {
                            if (items.length > 0) {
                                setValue(items[0].path);
                            }
                        }
                    }).show();
                });
                @endif
                $selector.find('.tools .close').on('click', function () {
                    setValue('');
                });
                $selector.find('.tools .preview').on('click', function () {
                    window.api.dialog.preview($field.find('[name="{{$name}}"]').val());
                });
            });
        </script>
        @if(!empty($help))
            <div class="help">{!! $help !!}</div>
        @endif
    </div>
</div>
