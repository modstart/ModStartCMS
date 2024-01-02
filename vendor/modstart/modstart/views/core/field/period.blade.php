<div class="line" data-field id="{{$id}}">
    <div class="label">
        {!! in_array('required',$rules)?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}
    </div>
    <div class="field">
        <input type="hidden"
               name="{{$name}}"
               id="{{$id}}Input"
               value="{{null===$value?$defaultValue:$value}}" />
        <div>
            <?php
            $v = $value;
            if(null===$v){
                $v = $defaultValue;
            }
            $valuePcs = ( $v ? explode(':',$v): [] );
            ?>
            <input type="number"
                   {{$readonly?'readonly':''}}
                   class="form  tw-text-center"
                   style="width:6em;"
                   id="{{$id}}Hour"
                   min="0"
                   value="{{isset($valuePcs[0])?$valuePcs[0]:'00'}}" />
            时
            <input type="number"
                   {{$readonly?'readonly':''}}
                   class="form tw-text-center"
                   style="width:6em;"
                   id="{{$id}}Minute"
                   min="0"
                   max="59"
                   value="{{isset($valuePcs[1])?$valuePcs[1]:'00'}}" />
            分
            <input type="number"
                   {{$readonly?'readonly':''}}
                   class="form tw-text-center"
                   style="width:6em;"
                   id="{{$id}}Second"
                   min="0"
                   max="59"
                   value="{{isset($valuePcs[2])?$valuePcs[2]:'00'}}" />
            秒
        </div>
        @if(!empty($help))
            <div class="help">{!! $help !!}</div>
        @endif
    </div>
</div>
<script>
    $(function(){
        var calc = function(){
            var pcs = [
                parseInt($('#{{$id}}Hour').val()),
                parseInt($('#{{$id}}Minute').val()),
                parseInt($('#{{$id}}Second').val())
            ];
            for(var i=0;i<3;i++){
                if(!pcs[i] || pcs[i]<0){
                    pcs[i] = '00';
                }else if(pcs[i]<10){
                    pcs[i] = '0' + pcs[i];
                }
            }
            $('#{{$id}}Input').val(pcs.join(':'));
        };
        $('#{{$id}}Hour,#{{$id}}Minute,#{{$id}}Second').on('change',calc);
        calc();
    });
</script>
