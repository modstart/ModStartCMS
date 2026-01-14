<?php $parents = \ModStart\Core\Util\TreeUtil::nodesChain($nodes, $value, 'id', 'pid'); ?>
@if(!empty($parents))
    @foreach($parents as $pIndex=>$p)
        @if($pIndex) / @endif
        {{$p['title']}}
    @endforeach
@else
    @if($value)
        {{$value}}
    @else
        <span class="ub-text-muted">-</span>
    @endif
@endif
