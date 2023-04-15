<div class="line">
    <div class="label">
        @if($tip)
            <a class="ub-text-muted" href="javascript:;" data-tip-popover="{{$tip}}"><i class="iconfont icon-warning"></i></a>
        @endif
        {{$label}}
    </div>
    <div class="field">
        <div style="box-shadow:0 0 2px #EEE;background:#FFF;border-radius:3px;padding:0 0 10px 0;">
            <div id="{{$name}}Tree"></div>
        </div>
    </div>
</div>
<script>
    layui.use('tree', function () {
        var tree = layui.tree;
        var ids = {!! json_encode($value) !!};
        var filter = function (nodes) {
            var newNodes = [];
            for (var i = 0; i < nodes.length; i++) {
                var item = {
                    id: nodes[i].id,
                    title: nodes[i].title,
                    spread: nodes[i].spread
                };
                if (nodes[i].children && nodes[i].children.length) {
                    item.children = filter(nodes[i].children);
                }
                if (ids.indexOf(item.id) >= 0 || (!item.id && ('children' in item) && item.children.length)) {
                    newNodes.push(item);
                }
            }
            return newNodes;
        };
        tree.render({
            id: '{{$name}}Tree',
            elem: '#{{$name}}Tree',
            data: filter({!! json_encode($nodes) !!})
        });
    });
</script>
