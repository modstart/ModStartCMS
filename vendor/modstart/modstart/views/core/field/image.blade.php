<div class="line" id="{{$id}}">
    <div class="label">
        {!! in_array('required',$rules)?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}
    </div>
    <div class="field">
        <input type="hidden"
               {{$readonly?'readonly':''}}
               class="form"
               name="{{$name}}"
               placeholder="{{$placeholder}}"
               value="{{$value}}"/>
        <div id="{{$name}}Selector" style="padding-left:1px;">
            <div class="ub-image-selector @if(!empty($value)) has-value @endif" style="vertical-align:bottom;">
                <div class="tools">
                    <a href="javascript:;" class="action close" data-close><i class="iconfont icon-close"></i></a>
                    <a href="javascript:;" class="action preview" data-preview><i class="iconfont icon-eye"></i></a>
                </div>
                @if(!empty($value))
                    <div class="cover ub-cover-1-1 contain" style="background-image:url({{$value}});"></div>
                @else
                    <div class="cover ub-cover-1-1 contain"
                         style="background-image:url(@asset('asset/image/none.svg'));"></div>
                @endif
            </div>
            <div id="{{$id}}Uploader" class="ub-upload-button" style="display:inline-block;height:1.65rem;vertical-align:bottom;line-height:1.65rem;overflow:visible;"></div>
            @if($mode=='default')
                <a href="javascript:;" class="btn" data-gallery style="display:inline-block;vertical-align:bottom;">
                    <i class="iconfont icon-category"></i>
                    {{L('Image Gallery')}}
                </a>
            @endif
        </div>
        {!! \ModStart\ModStart::js('asset/common/uploadButton.js') !!}
        <script>
            $(function () {
                var $field = $('#{{$id}}');
                var $selector = $('#{{$name}}Selector .ub-image-selector');
                var $gallery = $('#{{$name}}Selector [data-gallery]')
                function setValue(path) {
                    $field.find('[name="{{$name}}"]').val(path);
                    if (path) {
                        $selector.find('.cover').css('backgroundImage', "url(" + path + ")");
                        $selector.addClass('has-value');
                    } else {
                        $selector.find('.cover').css('backgroundImage', "url( @asset('asset/image/none.svg') )");
                        $selector.removeClass('has-value');
                    }
                }
                $selector.find('.tools .close').on('click', function () {
                    setValue('');
                });
                $selector.find('.tools .preview').on('click', function () {
                    window.api.dialog.preview($field.find('[name="{{$name}}"]').val());
                });
                window.api.uploadButton('#{{$id}}Uploader', {
                    text: '<a href="javascript:;" class="btn" style="display:inline-block;vertical-align:top;"><i class="iconfont icon-upload"></i> {{L("Local Upload")}}</a>',
                    server: "{{$server}}",
                    extensions: {!! json_encode(join(',',config('data.upload.image.extensions'))) !!},
                    sizeLimit: {!! json_encode(config('data.upload.image.maxSize')) !!},
                    chunkSize: {!! json_encode(\ModStart\Core\Util\EnvUtil::env('uploadMaxSize')) !!},
                    showFileQueue: true,
                    fileNumLimit: 1,
                    callback: function (file, me) {
                        MS.api.post("{{$server}}", {
                            action: "{{$mode=='raw'?'saveRaw':'save'}}",
                            path: file.path,
                            name: file.name,
                            size: file.size,
                            categoryId: 0
                        }, function(res){
                            MS.api.defaultCallback(res,{success:function(res){
                                setValue(res.data.fullPath);
                            }});
                        });
                    },
                    finish: function () {
                    }
                });
                @if($mode=='default')
                    $gallery.on('click', function () {
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
            });
        </script>
        @if(!empty($help))
            <div class="help">{!! $help !!}</div>
        @endif
    </div>
</div>
