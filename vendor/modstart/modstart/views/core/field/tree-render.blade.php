<script>
    layui.use('tree', function () {
        var tree = layui.tree;
        var ids = {!! \ModStart\Core\Util\SerializeUtil::jsonEncode(null===$value?(null===$defaultValue?[]:$defaultValue):$value) !!};
        var isChildrenMatch = function(nodes){
            if(!nodes || !nodes.length){
                return false;
            }
            for(var i=0;i<nodes.length;i++){
                if(ids.indexOf(nodes[i].id)>=0){
                    return true;
                }
                if(nodes[i].children && nodes[i].children.length){
                    if(isChildrenMatch(nodes[i].children)){
                        return true;
                    }
                }
            }
            return false;
        };
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
                if (ids.indexOf(item.id) >= 0
                    || (!item.id && ('children' in item) && item.children.length)
                    || isChildrenMatch(item.children)
                ) {
                    newNodes.push(item);
                }
            }
            return newNodes;
        };
        tree.render({
            id: '{{$renderId}}',
            elem: '#{{$renderId}}',
            data: filter({!! \ModStart\Core\Util\SerializeUtil::jsonEncode($nodes) !!})
        });
    });
</script>
