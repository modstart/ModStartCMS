<div class="line">
    <div class="label">
        {!! in_array('required',$rules)?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}:
    </div>
    <div class="field">
        <div id="{{$id}}Input">
            <input type="hidden" name="{{$name}}" :value="jsonValue" />
            <table class="ub-table">
                <tbody>
                    @foreach($fields as $f)
                    <tr>
                        <td width="1%" class="ub-text-truncate">{{empty($f['title'])?$f['name']:$f['title']}}</td>
                        <td>
                            @if($f['type']=='switch')
                                <el-switch v-model="value['{{$f['name']}}']"></el-switch>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if(!empty($help))
            <div class="help">{!! $help !!}</div>
        @endif
    </div>
</div>
{{ \ModStart\ModStart::js('asset/vendor/vue.js') }}
{{ \ModStart\ModStart::js('asset/vendor/element-ui/index.js') }}
{{ \ModStart\ModStart::css('asset/vendor/element-ui/index.css') }}
<script>
    $(function () {
        var app = new Vue({
            el: '#{{$id}}Input',
            data: {
                value: Object.assign({!! $defaultValue?json_encode($defaultValue):'{}' !!},{!! empty($value)?json_encode($defaultValue?$defaultValue:new \stdClass()):json_encode($value) !!}),
            },
            computed:{
                jsonValue:function(){
                    return JSON.stringify(this.value);
                }
            }
        });
    });
</script>
