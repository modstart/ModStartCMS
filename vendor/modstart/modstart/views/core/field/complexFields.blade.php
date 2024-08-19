<div class="line" data-field="{{$name}}">
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
                            @elseif($f['type']=='image')
                                <image-selector v-model="value['{{$f['name']}}']"></image-selector>
                            @elseif($f['type']=='number')
                                <el-input-number v-model="value['{{$f['name']}}']" size="mini"></el-input-number>
                            @elseif($f['type']=='slider')
                                <el-slider v-model="value['{{$f['name']}}']" size="mini"
                                           :min="{{$f['min']}}" :max="{{$f['max']}}" :step="{{$f['step']}}"
                                ></el-slider>
                            @elseif($f['type']=='link')
                                <div class="tw-flex">
                                    <div class="tw-flex-grow">
                                        <el-input v-model="value['{{$f['name']}}']"
                                                  placeholder="{{empty($f['placeholder'])?'':$f['placeholder']}}"
                                                  size="mini"></el-input>
                                    </div>
                                    <div>
                                        <el-button size="mini" @click="doSelectLink('{{$f['name']}}')">选择</el-button>
                                    </div>
                                </div>
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
{{ \ModStart\ModStart::js('asset/entry/basic.js') }}
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
                },
                doSelectLink(index,name,param){
                    window.__selectorDialog = new window.api.selectorDialog({
                        server: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode($linkServer) !!},
                        callback: (items) => {
                            if (items.length > 0) {
                                this.value[index][name] = items[0].link;
                            }
                        }
                    }).show();
                }
            }
        });
    });
</script>
