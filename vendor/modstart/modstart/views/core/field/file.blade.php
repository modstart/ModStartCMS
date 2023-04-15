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
               name="{{$name}}"
               placeholder="{{$placeholder}}"
               value="{{$value}}"/>
        <div id="{{$name}}Selector">
            <div class="ub-file-selector">
                <div class="ub-file-selector__items">
                    <div class="ub-file-selector__item {{empty($value)?'hidden':$value}}">
                        <div data-value class="ub-file-selector__value"></div>
                        <div data-close class="ub-file-selector__close">
                            <i class="iconfont icon-close"></i>
                        </div>
                    </div>
                </div>
                <div class="ub-file-selector__action">
                    <div id="{{$id}}Uploader" class="ub-upload-button"></div>
                </div>
                @if($mode=='default')
                    <div class="ub-file-selector__action">
                        <a href="javascript:;" class="btn" data-gallery>
                            <i class="iconfont icon-category"></i>
                            {{L('File Gallery')}}
                        </a>
                    </div>
                @endif
            </div>
        </div>
        {!! \ModStart\ModStart::js('asset/common/uploadButton.js') !!}
        <script>
            $(function () {
                var $field = $('#{{$id}}');
                var $selector = $('#{{$name}}Selector .ub-file-selector');
                var $gallery = $('#{{$name}}Selector [data-gallery]')
                function setValue(path) {
                    path = path || '';
                    $field.find('[name="{{$name}}"]').val(path);
                    $selector.find('[data-value]').html(path);
                    $selector.find('.ub-file-selector__item').toggleClass('hidden', !path);
                }
                $selector.find('[data-close]').on('click', function () {
                    setValue('');
                });
                setValue($field.find('[name="{{$name}}"]').val());
                MS.uploadButton('#{{$id}}Uploader', {
                    text: '<a href="javascript:;" class="btn"><i class="iconfont icon-upload"></i> {{L("Local Upload")}}</a>',
                    server: "{{$server}}",
                    extensions: {!! json_encode(join(',',config('data.upload.file.extensions'))) !!},
                    sizeLimit: {!! json_encode(config('data.upload.file.maxSize')) !!},
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
                    window.__selectorDialog = new MS.selectorDialog({
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
