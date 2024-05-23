<div class="line" data-field="{{$name}}" id="{{$id}}">
    <div class="label">
        {!! in_array('required',$rules)?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}
    </div>
    <div class="field">
        <div id="{{$id}}Input" class="tw-rounded tw-bg-white ub-border">
            <input type="hidden" name="{{$name}}" :value="jsonValue" />
            @if($viewMode=='mini')
                <div class="tw-inline-block tw-w-32 tw-p-2" v-for="(valueItem,valueIndex) in value">
                    <el-input placeholder="请输入内容" size="mini" v-model="value[valueIndex]">
                        @if(empty($countFixed))
                            <template slot="append">
                                <a href="javascript:;" class="ub-text-danger" @click="value.splice(valueIndex,1)"><i class="iconfont icon-trash"></i></a>
                            </template>
                        @endif
                    </el-input>
                </div>
                @if(empty($countFixed))
                    <a href="javascript:;" class="ub-text-muted" @click="value.push('')"><i class="iconfont icon-plus"></i> {{L('Add')}}</a>
                @endif
            @else
                <table class="ub-table tw-bg-white mini">
                    <body>
                    <tr v-for="(valueItem,valueIndex) in value">
                        <td>
                            <input type="text" v-model="value[valueIndex]" />
                        </td>
                        <td width="80" class="tw-text-right">
                            <a href="javascript:;" class="ub-text-primary" v-if="valueIndex>0" @click="doUp(value,valueIndex)"><i class="iconfont icon-direction-up"></i></a>
                            <a href="javascript:;" class="ub-text-primary" v-if="valueIndex<value.length-1" @click="doDown(value,valueIndex)"><i class="iconfont icon-direction-down"></i></a>
                            @if(empty($countFixed))
                                <a href="javascript:;" class="ub-text-danger" @click="value.splice(valueIndex,1)"><i class="iconfont icon-trash"></i></a>
                            @endif
                        </td>
                    </tr>
                    </body>
                </table>
                @if(empty($countFixed))
                    <a href="javascript:;" class="ub-text-muted" @click="value.push('')"><i class="iconfont icon-plus"></i> {{L('Add')}}</a>
                @endif
            @endif
        </div>
        @if(!empty($help))
            <div class="help">{!! $help !!}</div>
        @endif
    </div>
</div>
<script>
    {{ \ModStart\ModStart::js('asset/vendor/vue.js') }}
    {{ \ModStart\ModStart::js('asset/vendor/element-ui/index.js') }}
    {{ \ModStart\ModStart::css('asset/vendor/element-ui/index.css') }}
    $(function () {
        var app = new Vue({
            el: '#{{$id}}Input',
            data: function(){
                return {
                    value: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode(null===$value?(null===$defaultValue?[]:$defaultValue):$value) !!}
                };
            },
            computed:{
                jsonValue:function(){
                    return JSON.stringify(this.value);
                }
            },
            methods:{
                doUp:MS.collection.sort.up,
                doDown:MS.collection.sort.down
            }
        });
        $('#{{$id}}').data('app',app);
    });
</script>
