<div class="field auto" data-grid-filter-field="{{$id}}" data-grid-filter-field-column="{{$column}}">
    <div class="name">{{$label}}</div>
    <div class="input">
        <div id="{{$id}}App">
            <el-cascader v-model="value" size="mini" :options="optionTree" clearable
                         :props="{children:'_child',label:'title',value:'id',checkStrictly:true}"></el-cascader>
        </div>
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
                nodes: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode($field->nodes()) !!}
            },
            computed: {
                optionTree() {
                    return MS.tree.tree(this.nodes, 0, 'id', 'pid', 'sort');
                }
            }
        });
        $field.data('get', function () {
            var v = app.$data.value || [];
            return {
                '{{$column}}': {
                    eq: v.length>0 ? v[v.length - 1] : ''
                }
            };
        });
        $field.data('reset', function () {
            app.$data.value = [];
        });
        $field.data('setNodes', function (nodes) {
            this.value = [];
            app.$data.nodes = nodes;
        });
    });
</script>
