<div class="line">
    <div class="label">
        {!! in_array('required',$rules)?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}
    </div>
    <div class="field">
        <div id="{{$id}}Input">
            <input type="hidden" name="{{$name}}" :value="jsonValue" />
            <table class="ub-table border tw-bg-white">
                <tbody>
                    @foreach($fields as $f)
                    <tr>
                        <td width="1%" class="ub-text-truncate">
                            {{empty($f['title'])?$f['name']:$f['title']}}
                            @if(!empty($f['tip']))
                                <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$f['tip']}}"><i class="iconfont icon-warning"></i></a>
                            @endif
                        </td>
                        <td>
                            @if($f['type']=='switch')
                                <el-switch v-model="value['{{$f['name']}}']"></el-switch>
                            @elseif($f['type']=='text')
                                <el-input v-model="value['{{$f['name']}}']" size="mini"></el-input>
                            @elseif($f['type']=='icon')
                                <icon-input v-model="value['{{$f['name']}}']" :icons="icons" :inline="true"></icon-input>
                            @elseif($f['type']=='number')
                                <el-input-number v-model="value['{{$f['name']}}']" size="mini"></el-input-number>
                            @elseif($f['type']=='slider')
                                <el-slider v-model="value['{{$f['name']}}']" size="mini"
                                           :min="{{$f['min']}}" :max="{{$f['max']}}" :step="{{$f['step']}}"
                                ></el-slider>
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
                value: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode(null===$value?(null===$defaultValue?[]:$defaultValue):$value) !!},
                icons: []
            },
            mounted(){
                MS.api.post('{{$iconServer}}', {}, res => {
                    this.icons = res.data
                })
            },
            computed:{
                jsonValue:function(){
                    return JSON.stringify(this.value);
                }
            }
        });
    });
</script>
