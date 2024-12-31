<div class="line" data-field="{{$name}}" id="{{$id}}">
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
               value="{{null===$value?$defaultValue:$value}}"/>
        <div id="{{$name}}Selector">

            <div class="ub-file-selector">
                <div class="ub-file-selector__items">
                    @yield('fieldFilePreviewItem')
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
                @if(null!==$defaultValue)
                    <div class="ub-file-selector__action">
                        <a href="javascript:;" class="btn" data-reset-default>
                            <i class="iconfont icon-undo"></i>
                            {{L('Reset Default')}}
                        </a>
                    </div>
                @endif
            </div>

        </div>

        {!! \ModStart\Core\Hook\ModStartHook::fireInView('UploadScript',['source'=>'uploadButton','server'=>$server,'id'=>$id]); !!}
        {!! \ModStart\ModStart::js('asset/common/uploadButton.js') !!}
        <script>
            $(function () {
                window.__uploadCustomUpload = window.__uploadCustomUpload || {}
                var $field = $('#{{$id}}');
                var $input = $field.find('[name="{{$name}}"]');
                var $selector = $('#{{$name}}Selector');
                function setValue(path) {
                    path = path || '';
                    $input.val(path);
                    $selector.find('[data-value]').html(path);
                    $selector.find('[data-file-item]').toggleClass('hidden', !path);
                    var previewImage = path;
                    if(!previewImage){
                        previewImage = "@asset('asset/image/none.svg')";
                    }
                    $selector.find('[data-value-background]').css('background-image', 'url(' + previewImage + ')');
                    $selector.find('[data-image-preview]').attr('data-image-preview', previewImage);
                }
                function callSuccessCallback(path, param){
                    MS.eventManager && MS.eventManager.fireElementEvent($input[0], 'success', {path: path, param: param});
                }
                $selector.on('click', '[data-close]', function () {
                    setValue('');
                });
                @if(null!==$defaultValue)
                    $selector.on('click','[data-reset-default]',function(){
                        setValue({!! json_encode($defaultValue) !!});
                    });
                @endif
                setValue($input.val());
                MS.uploadButton('#{{$id}}Uploader', {
                    text: '<a href="javascript:;" class="btn"><i class="iconfont icon-upload"></i> {{L("Local Upload")}}</a>',
                    server: "{{$server}}",
                    extensions: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode(join(',',config('data.upload.'.$category.'.extensions'))) !!},
                    sizeLimit: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode(config('data.upload.'.$category.'.maxSize')) !!},
                    chunkSize: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode(\ModStart\Core\Util\EnvUtil::env('uploadMaxSize')) !!},
                    @if($category=='image')
                    compress:{
                        enable: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode(config('data.upload.image.compress',true)) !!},
                        maxWidthOrHeight: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode(config('data.upload.image.compressMaxWidthOrHeight',4000)) !!},
                        maxSize: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode(config('data.upload.image.compressMaxSize',4000)) !!}
                    },
                    @endif
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
                                callSuccessCallback(res.data.fullPath, {data: res.data});
                            }});
                        });
                    },
                    finish: function () {
                    },
                    customUpload: window.__uploadCustomUpload['{{$id}}'] || null
                });
                @if($mode=='default')
                    $selector.on('click', '[data-gallery]', function () {
                        window.__selectorDialog = new MS.selectorDialog({
                            server: '{{$server}}',
                            callback: function (items) {
                                if (items.length > 0) {
                                    setValue(items[0].path);
                                    callSuccessCallback(items[0].path);
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
