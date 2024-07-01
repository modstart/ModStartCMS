<div class="line" data-field id="{{$id}}">
    <div class="label">
        {!! in_array('required',$rules)?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}
    </div>
    <div class="field" >
        @if($readonly)
            <input type="hidden" name="{{$name}}" value="{{null===$value?$defaultValue:$value}}" />
        @endif
        <div class="layui-form tw-inline-block" lay-filter="{{$name}}">
            <select class="form" id="{{$id}}Select" name="{{$name}}"
                    @if($selectSearch) lay-search @else lay-ignore @endif
                    @if($readonly) disabled @endif>
                @foreach($options as $k=>$v)
                    @if(isset($v['label']))
                        <option value="{{$k}}" @if((null===$value&&$k==$defaultValue)||(null!==$value&&$k==$value)) selected @endif @if(isset($v['title'])) title="{{$v['title']}}" @endif>{{$v['label']}}</option>
                    @else
                        <option value="{{$k}}" @if((null===$value&&$k==$defaultValue)||(null!==$value&&$k==$value)) selected @endif>{{$v}}</option>
                    @endif
                @endforeach
            </select>
        </div>
        @if(!empty($help))
            <div class="help">{!! $help !!}</div>
        @endif
    </div>
</div>
<script>
    layui.use('form', function(){
        layui.form.render('select','{{$name}}');
        var firstOnChangeFired = false,
            firstRemoteLoad = true,
            $field = $('#{{$id}}'),
            $select = $field.find('select');
        @if(!empty($optionRemote))
            $field.on('remote-load',function(event, data){
                loadOptionRemote(data);
            });
            function loadOptionRemote(data){
                data = data || {};
                let initValue = $select.val();
                if('value' in data){
                    initValue = data.value;
                }else if( firstRemoteLoad ){
                    firstRemoteLoad = false;
                    initValue = {!! json_encode(null===$value?$defaultValue:$value) !!};
                }
                let callback = data.callback || function(){};
                delete data.callback;
                @if(!empty($onRemoteLoadJsFunction))
                    data = Object.assign(data,({!! $onRemoteLoadJsFunction !!})(initValue) || {} );
                @endif
                data = Object.assign({value:initValue},data);
                MS.api.postSuccess('{{$optionRemote}}',data,function(res){
                    var html = [];
                    html.push('<option value="">{{L('Please Select')}}</option>');
                    res.data.records.forEach(function(item){
                        var current = ( item.value+''===initValue+'' );
                        html.push('<option value="'+item.value+'" '+(current?'selected':'')+'>'+MS.util.specialchars(item.label)+'</option>');
                    });
                    $select.html(html.join(''));
                    if(!firstOnChangeFired || $select.val()!==initValue){
                        firstOnChangeFired = true;
                        $select.trigger('change');
                    }
                    callback();
                });
            };
        @endif
        $select.on('change',function(){
             @if(!empty($onValueChangeJsFunction))
                ({!! $onValueChangeJsFunction !!})( $select.val() );
            @endif
        });
        @if(!empty($optionRemote) && $optionRemoteAutoInit)
            loadOptionRemote();
        @endif
    });
</script>
