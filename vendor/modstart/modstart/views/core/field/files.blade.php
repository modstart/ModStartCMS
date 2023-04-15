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
               value="{{json_encode($value)}}"/>
        <div class="ub-file-selector">
            <div class="ub-file-selector__items"></div>
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
        {!! \ModStart\ModStart::js('asset/common/uploadButton.js') !!}
        @if(!empty($help))
            <div class="help">{!! $help !!}</div>
        @endif
    </div>
</div>
<script>
    $(function () {
        var $field = $('#{{$id}}');
        var $input = $field.find('input');
        var $selector = $field.find('.ub-file-selector');
        var $itemsSelector = $selector.find('.ub-file-selector__items');
        var files = [];
        try {
            files = JSON.parse($input.val());
        } catch (e) {
        }
        if(!files){
            files = [];
        }
        var render = function(){
            $itemsSelector.html('');
            var $item;
            for(var i =0;i<files.length;i++){
                $item = $('<div class="ub-file-selector__item" data-index="'+i+'">'
                            +'<div data-value class="ub-file-selector__value">'+files[i]+'</div>'
                            +'<div data-close class="ub-file-selector__close">'
                                +'<i class="iconfont icon-close"></i>'
                            +'</div>'
                         +'</div>');
                $itemsSelector.append($item);
            }
            $input.val(JSON.stringify(files));
        };
        $itemsSelector.on('click','[data-close]',function(){
            var index = parseInt($(this).closest('[data-index]').attr('data-index'));
            files.splice(index,1);
            render();
            return false;
        });
        render();
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
                        files.push(res.data.fullPath);
                        render();
                    }});
                });
            },
            finish: function () {
            }
        });
        @if($mode=='default')
        $selector.on('click','[data-gallery]',function(){
            window.__selectorDialog = new MS.selectorDialog({
                server: '{{$server}}',
                callback: function (items) {
                    items.forEach(o=>{
                        files.push(o.fullPath);
                    })
                    render();
                }
            }).show();
            return false;
        });
        @endif
    });
</script>
