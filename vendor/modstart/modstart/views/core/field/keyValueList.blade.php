<div class="line">
    <div class="label">
        {!! in_array('required',$rules)?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}
    </div>
    <div class="field">
        <div id="{{$id}}Input" v-cloak>
            <input type="hidden" name="{{$name}}" :value="jsonValue" />
            <table class="ub-table border border-all">
                <thead>
                <tr>
                    <th width="200">{{$keyTitle}}</th>
                    <th>{{$valueTitle}}</th>
                    <th width="50">&nbsp;</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(v,vIndex) in value">
                    <td>
                        <input type="text" v-model="value[vIndex].{{$keyLabel}}" placeholder="{{$keyPlaceholder}}" />
                    </td>
                    <td>
                        <input type="text" v-model="value[vIndex].{{$valueLabel}}" placeholder="{{$valuePlaceholder}}" />
                    </td>
                    <td class="tw-text-center">
                        <a href="javascript:;" class="ub-text-danger" @click="value.splice(vIndex,1)">
                            <i class="iconfont icon-trash"></i>
                        </a>
                    </td>
                </tr>
                </tbody>
                <tbody>
                <tr>
                    <td colspan="3">
                        <a href="javascript:;" class="ub-text-muted" @click="doValueAdd">
                            <i class="iconfont icon-plus"></i>
                            {{L('Add')}}
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
<script>
    $(function () {
        var app = new Vue({
            el: '#{{$id}}Input',
            data: {
                value: {!! json_encode((null===$value)?$defaultValue:$value) !!}
            },
            computed:{
                jsonValue:function(){
                    return JSON.stringify(this.value);
                }
            },
            methods:{
                doValueAdd(){
                    this.value.push({ {{$keyLabel}} :'', {{$valueLabel}}:'' });
                }
            }
        });
    });
</script>
