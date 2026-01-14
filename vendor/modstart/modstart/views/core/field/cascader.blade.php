<div class="line" data-field id="{{$id}}">
    <div class="label">
        {!! in_array('required',$rules)?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}
    </div>
    <div class="field" >
        <div id="{{$id}}App">
            <input type="hidden" name="{{$name}}" :value="valueJson" />
            <el-cascader v-model="value" size="small" :options="optionTree"
                         style="width:100%;"
                         :props="{children:'_child',label:'title',value:'id',checkStrictly:true}"></el-cascader>
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
        var $field = $('[data-grid-filter-field={{$id}}]');
        var app = new Vue({
            el: '#{{$id}}App',
            data: {
                value: [],
                nodes: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode($nodes) !!}
            },
            computed: {
                valueJson(){
                    if(this.value.length>0){
                        return this.value[this.value.length-1];
                    }
                    return 0;
                },
                optionTree() {
                    return MS.tree.tree(this.nodes, 0, 'id', 'pid', 'sort');
                }
            },
            mounted(){
                let initValue = {!! \ModStart\Core\Util\SerializeUtil::jsonEncode(null===$value?$defaultValue:$value) !!};
                if(initValue){
                    this.value = MS.tree.findAncestorIds(this.nodes, initValue, 'id', 'pid');
                }
            }
        });
    });
</script>
