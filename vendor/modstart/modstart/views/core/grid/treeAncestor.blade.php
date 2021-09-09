<span class="tree-arrow-icon ub-text-muted"><i class="icon iconfont icon-angle-right"></i></span>
<a class="ub-text-primary" href="?" title="{{L('Manage')}}">
    <i class="icon iconfont icon-sign"></i>
</a>
<a class="ub-text-primary" href="?" title="{{L('Manage')}}">
    {{L('Root')}}
</a>
@foreach($treeAncestors as $treeAncestor)
    <span class="tree-arrow-icon ub-text-muted"><i class="icon iconfont icon-angle-right"></i></span>
    <a class="ub-text-primary" href="?_pid={{$treeAncestor[$grid->repository()->getKeyName()]}}" title="{{L('Manage')}}">
        <i class="icon iconfont icon-sign"></i>
    </a>
    <a class="ub-text-primary" href="?_pid={{$treeAncestor[$grid->repository()->getKeyName()]}}" title="{{L('Manage')}}">
        {{$treeAncestor[$grid->repository()->getTreeTitleColumn()]}}
    </a>
@endforeach
