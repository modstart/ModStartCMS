<div class="line" id="{{$id}}">
    <div class="label">
        {!! in_array('required',$rules)?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        {{$label}}:
    </div>
    <div class="field">
        <div id="{{$id}}Selector">
            <input type="hidden" name="{{$name}}" value="{{json_encode($value,JSON_UNESCAPED_UNICODE)}}"/>
            @if(!\Module\Vendor\Provider\VideoStream\VideoStreamProvider::first())
                <span class="ub-text-warning">请先安装视频点播模块</span>
            @else
                <select data-driver>
                    @foreach(\Module\Vendor\Provider\VideoStream\VideoStreamProvider::all() as $provider)
                        <option value="{{$provider->name()}}"
                                data-server="{{$provider->dialogUrl($scope)}}">{{$provider->title()}}</option>
                    @endforeach
                </select>
                <div class="ub-file-selector" style="display:inline-block;">
                    <div class="ub-file-selector__value" data-value>
                        {{empty($value['name'])?L('None'):$value['name']}}
                    </div>
                    <div class="ub-file-selector__close {{empty($value['name'])?'hidden':''}}" data-close>
                        <i class="iconfont icon-close"></i>
                    </div>
                    <div class="ub-file-selector__action {{empty($value['name'])?'':'hidden'}}" data-gallery>
                        <a href="javascript:;" class="btn">
                            <i class="iconfont icon-category"></i>
                            上传/选择视频
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <script>
            $(function () {
                var $selector = $('#{{$id}}Selector');

                function setValue(driver, name, path) {
                    $selector.find('[name={{$name}}]').val(JSON.stringify({driver: driver, name: name, path: path}));
                    if (name) {
                        $selector.find('[data-value]').html(name);
                        $selector.find('.ub-file-selector__action').addClass('hidden');
                        $selector.find('.ub-file-selector__close').removeClass('hidden');
                    } else {
                        $selector.find('[data-value]').html("{{L('None')}}");
                        $selector.find('.ub-file-selector__action').removeClass('hidden');
                        $selector.find('.ub-file-selector__close').addClass('hidden');
                    }
                }

                $selector.find('[data-gallery]').on('click', function () {
                    new MS.selectorDialog({
                        server: $selector.find('[data-driver] option:selected').attr('data-server'),
                        callback: function (items) {
                            if (items.length > 0) {
                                setValue($selector.find('[data-driver]').val(), items[0].name.trim(), items[0].path.trim());
                            }
                        }
                    }).show();
                });
                $selector.find('[data-driver]').on('change', function () {
                    setValue('', '', '');
                });
                $selector.find('[data-close]').on('click', function () {
                    setValue('', '', '');
                });
            });
        </script>
    </div>
</div>
