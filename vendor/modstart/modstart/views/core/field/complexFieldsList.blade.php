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
            <table class="ub-table">
                <thead>
                    <tr>
                        @foreach($fields as $f)
                            <th>{{empty($f['title'])?$f['name']:$f['title']}}</th>
                        @endforeach
                        <td>&nbsp;</td>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(v,vIndex) in value">
                        @foreach($fields as $f)
                            <td>
                                @if($f['type']=='switch')
                                    <el-switch v-model="value[vIndex]['{{$f['name']}}']" />
                                @elseif($f['type']=='text')
                                    <el-input v-model="value[vIndex]['{{$f['name']}}']" size="mini" />
                                @elseif($f['type']=='icon')
                                    <icon-input v-model="value[vIndex]['{{$f['name']}}']" :icons="icons" :inline="true" />
                                @elseif($f['type']=='number')
                                    <el-input-number v-model="value[vIndex]['{{$f['name']}}']" size="mini" />
                                @endif
                            </td>
                        @endforeach
                        <td>
                            <a href="javascript:;" class="ub-text-muted" @click="value.splice(vIndex,1)">
                                <i class="iconfont icon-trash"></i>
                            </a>
                        </td>
                    </tr>
                </tbody>
                <tbody>
                    <tr>
                        <td colspan="{{count($fields)+1}}">
                            <a href="javascript:;" class="ub-text-muted" @click="doValueAdd">
                                <i class="iconfont icon-plus"></i>
                            </a>
                        </td>
                    </tr>
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
                value: {!! $value?json_encode($value):($defaultValue?json_encode($defaultValue):'[]') !!},
                icons: []
            },
            mounted(){
                this.$api.post('{{$iconServer}}', {}, res => {
                    this.icons = res.data
                })
            },
            computed:{
                jsonValue:function(){
                    return JSON.stringify(this.value);
                }
            },
            methods:{
                doValueAdd(){
                    this.value.push({!! json_encode($valueItem) !!})
                }
            }
        });
    });
</script>
