<div class="line" id="{{$id}}">
    <div class="label">
        {!! in_array('required',$rules)?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}
    </div>
    <div class="field">
        <div id="{{$id}}Input">
            <input type="hidden"
                   {{$readonly?'readonly':''}}
                   class="form"
                   name="{{$name}}"
                   placeholder="{{$placeholder}}"
                   :value="value"/>
            <icon-input v-model="value" :icons="icons" :inline="true"></icon-input>
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
    {{ \ModStart\ModStart::js('asset/entry/basic.js') }}
    $(function () {
        var app = new window.__vueManager.Vue({
            el: '#{{$id}}Input',
            data: {
                value: {!! json_encode(null===$value?$defaultValue:$value) !!},
                icons: [],
            },
            computed:{
                jsonValue:function(){
                    return JSON.stringify(this.value);
                }
            },
            mounted(){
                this.$api.post('{{$server}}', {}, res => {
                    this.icons = res.data
                })
            },
            methods:{
            }
        });
    });
</script>
