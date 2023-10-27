<div class="field auto" data-grid-filter-field="{{$id}}" data-grid-filter-field-column="{{$column}}">
    <div class="name">{{$label}}</div>
    <div class="input">
        <div id="{{$id}}App">
            <el-cascader v-model="value" size="mini" :options="optionTree"
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
            v = v.length > 0 ? v[v.length - 1] : 0;
            if (v > 0) {
                v = MS.tree.findChildrenIdsIncludeSelf(app.$data.nodes, v, 'id', 'pid');
            } else {
                v = [];
            }
            return {
                '{{$column}}': {
                    has: v
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
        @if(0)
        $field.data('init', function (data) {
            for (var i = 0; i < data.length; i++) {
                for (var k in data[i]) {
                    if (k === '{{$column}}' && ('has' in data[i][k])) {
                        $('#{{$id}}_select').val(data[i][k].has);
                    }
                }
            }
        });
        @endif
    });
</script>
