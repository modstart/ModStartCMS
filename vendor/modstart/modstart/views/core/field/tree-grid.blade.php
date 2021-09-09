<div style="background:#FFF;box-shadow:0 0 2px #EEE;border-radius:3px;padding:0 0 10px 0;display:inline-block;">
    <div id="{{$name}}Tree_{{$_index}}"></div>
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
            id: '{{$name}}Tree_{{$_index}}',
            elem: '#{{$name}}Tree_{{$_index}}',
            data: filter({!! json_encode($nodes) !!})
        });
    });
</script>