<div class="line" id="{{$id}}">
    <div class="label">
        {!! str_contains($rules,'required')?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}:
    </div>
    <div class="field">
        <input type="hidden"
               {{$readonly?'readonly':''}}
               class="form"
               name="{{$name}}"
               placeholder="{{$placeholder}}"
               value="{{$value}}"/>
        <div id="{{$name}}Selector" class="ub-file-selector">
            <div class="ub-file-selector__value" data-value>
                {{empty($value)?L('None'):$value}}
            </div>
            <div data-close class="ub-file-selector__close {{empty($value)?'hidden':''}}">
                <i class="iconfont icon-close"></i>
            </div>
            <div class="ub-file-selector__action {{empty($value)?'':'hidden'}}">
                <div id="{{$id}}Uploader" class="ub-upload-button"></div>
            </div>
            @if($mode=='default')
                <div class="ub-file-selector__action {{empty($value)?'':'hidden'}}">
                    <a href="javascript:;" class="btn" data-gallery>
                        <i class="iconfont icon-category"></i>
                        {{L('Video Gallery')}}
                    </a>
                </div>
            @endif
        </div>
        {!! \ModStart\ModStart::js('asset/common/uploadButton.js') !!}
        <script>
            $(function () {
                var $field = $('#{{$id}}');
                var $selector = $('#{{$name}}Selector');
                var $gallery = $selector.find('[data-gallery]');
                function setValue(path) {
                    try {
                        $field.find('[name="{{$name}}"]').val(path);
                    } catch (e) {
                    }
                    if (path) {
                        $selector.find('[data-value]').html(path);
                        $selector.find('.ub-file-selector__action').addClass('hidden');
                        $selector.find('.ub-file-selector__close').removeClass('hidden');
                    } else {
                        $selector.find('[data-value]').html("{{L('None')}}");
                        $selector.find('.ub-file-selector__action').removeClass('hidden');
                        $selector.find('.ub-file-selector__close').addClass('hidden');
                    }
                }
                $selector.find('[data-close]').on('click', function () {
                    setValue('');
                });
                window.api.uploadButton('#{{$id}}Uploader', {
                    text: '<a href="javascript:;" class="btn" style="display:inline-block;vertical-align:bottom;"><i class="iconfont icon-upload"></i> {{L("Local Upload")}}</a>',
                    server: "{{$server}}",
                    extensions: {!! json_encode(join(',',config('data.upload.video.extensions'))) !!},
                    sizeLimit: {!! json_encode(config('data.upload.video.maxSize')) !!},
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
