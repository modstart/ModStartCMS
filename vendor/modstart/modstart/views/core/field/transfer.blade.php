@if(null!==$label)
<div class="line" data-field="{{$name}}" id="{{$id}}">
    <div class="label">
        {!! in_array('required',$rules)?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}
    </div>
    <div class="field">
@endif
        <div class="layui-form" lay-filter="{{$name}}">
            <div id="{{$id}}Transfer"></div>
            <input type="hidden" name="{{$name}}" value="" />
        </div>
        @if(!empty($help))
            <div class="help">{!! $help !!}</div>
        @endif
@if(null!==$label)
    </div>
</div>
@endif
<script>
    <?php
    $optionMap = [];
    foreach ($options as $k=>$v){
        if(isset($v['label'])){
            $optionMap[$v['label']] = $v['title'];
        }else{
            $optionMap[$k] = $v;
        }
    }
    $tData = [];
    $tDataMap = [];
    $d = [];
    if(null!==$value){
        $d = $value;
    }elseif(null!==$defaultValue){
        $d = $defaultValue;
    }
    foreach ($d as $dd){
        if(isset($optionMap[$dd])){
            $tData[] = ['value'=>$dd,'title'=>$optionMap[$dd],];
            $tDataMap[$dd] = true;
        }
    }
    foreach ($optionMap as $k=>$v){
        if(!isset($tDataMap[$k])){
            $tData[] = ['value'=>$k,'title'=>$v,];
        }
    }
    ?>
    layui.use(function(){
        layui.transfer.render({
            id: '{{$id}}Transfer',
            elem: '#{{$id}}Transfer',
            title: ['未启用', '已启用'],
            data: {!! json_encode($tData) !!},
            value: {!! json_encode($d) !!},
            height: 210,
            onchange: function(data,index){
                const value = layui.transfer.getData('{{$id}}Transfer').map(function (item) {
                    return item.value;
                });
                $('[name="{{$name}}"]').val(JSON.stringify(value));
            }
        });
        $('[name="{{$name}}"]').val(JSON.stringify({!! json_encode($d) !!}));
    });
</script>
