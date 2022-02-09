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
        <div id="{{$name}}Selector">
            <div class="ub-video-selector" style="display:inline-block;border:1px solid #EEE;position:relative;border-radius:0.2rem;line-height:1.3rem;padding:0 2rem 0 0.5rem;vertical-align:bottom;">
                <div data-value>{{empty($value)?L('None'):$value}}</div>
                <a data-close href="javascript:;" style="{{$value?'display:inline-block;':'display:none;'}}position:absolute;right:0px;top:0px;line-height:1.3rem;width:1rem;text-align:center;color:#999;"><i class="iconfont icon-close"></i></a>
            </div>
            <div id="{{$id}}Uploader" class="ub-upload-button" style="display:inline-block;height:1.35rem;vertical-align:bottom;line-height:1.35rem;"></div>
            @if($mode=='default')
                <a href="javascript:;" class="btn" data-gallery style="display:inline-block;vertical-align:bottom;">
                    <i class="iconfont icon-category"></i>
                    {{L('Video Gallery')}}
                </a>
            @endif
        </div>
        {!! \ModStart\ModStart::js('asset/common/uploadButton.js') !!}
        <script>
            $(function () {
                var $field = $('#{{$id}}');
                var $selector = $('#{{$name}}Selector .ub-video-selector');
                var $gallery = $('#{{$name}}Selector [data-gallery]')
                function setValue(path) {
                    try {
                        $field.find('[name="{{$name}}"]').val(path);
                    } catch (e) {
                    }
                    if (path) {
                        $selector.find('[data-value]').html(path);
                        $selector.find('[data-close]').css('display','inline-block');
                    } else {
                        $selector.find('[data-value]').html("{{L('None')}}");
                        $selector.find('[data-close]').css('display','none');
                    }
                }
                $selector.find('[data-close]').on('click', function () {
                    setValue('');
                });
                window.api.uploadButton('#{{$id}}Uploader', {
                    text: '<a href="javascript:;" class="btn" style="display:inline-block;vertical-align:bottom;"><i class="iconfont icon-upload"></i> {{L("Local Upload")}}</a>',
                    swf: "@asset('asset/vendor/webuploader/Uploader.swf')",
                    server: "{{$server}}",
                    extensions: window.__dataConfig.category.video.extensions.join(','),
                    sizeLimit: window.__dataConfig.category.video.maxSize,
                    chunkSize: window.__dataConfig.chunkSize,
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
