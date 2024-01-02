<div class="line" data-field id="{{$id}}">
    <div class="label">
        {!! in_array('required',$rules)?'<span class="ub-text-danger ub-text-bold">*</span>':'' !!}
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}
    </div>
    <div class="field">
        <input type="hidden"
               name="{{$name}}"
               value='{{\ModStart\Core\Util\SerializeUtil::jsonEncode(null==$value?($defaultValue?$defaultValue:[]):$value)}}'/>
        <div style="border:1px solid #DDD;border-radius:3px;padding:0 0 0.5rem 0;background:#FFF;">
            <div id="{{$name}}Tree"></div>
        </div>
        @if(!empty($help))
            <div class="help">{!! HtmlU !!}</div>
        @endif
    </div>
</div>
<script>
    layui.use('tree', function () {
        var getNodeIds = function (nodes) {
            var ids = [];
            for (var i = 0; i < nodes.length; i++) {
                if (nodes[i].id) {
                    ids.push(nodes[i].id);
                }
                if (('children' in nodes[i]) && nodes[i].children.length > 0) {
                    ids = ids.concat(getNodeIds(nodes[i].children));
                }
            }
            return ids;
        };
        var tree = layui.tree;
        var inst1 = tree.render({
            id: '{{$name}}Tree',
            elem: '#{{$name}}Tree',
            showCheckbox: true,
            independentEnable: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode(!empty($independentEnable)) !!},
            data: {!! \ModStart\Core\Util\SerializeUtil::jsonEncode($nodes) !!},
            oncheck: function (obj) {
                var nodes = tree.getChecked('{{$name}}Tree');
                $('[name={{$name}}]').val(JSON.stringify(getNodeIds(nodes)));
            }
        });
        var value = JSON.parse($('[name={{$name}}]').val());
        tree.setChecked('{{$name}}Tree', value);
    });
</script>
