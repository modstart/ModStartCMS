<?php $valueLabel = isset($valueMap[$value])?$valueMap[$value]:$value; ?>
@if(!empty($colorMap))
    <span class="ub-text-{{isset($colorMap[$value])?$colorMap[$value]:'default'}}">
        <i class="iconfont icon-dot-sm ub-text-{{isset($colorMap[$value])?$colorMap[$value]:'default'}}"></i>{{$valueLabel}}
    </span>
@else
    <span>
        {{$valueLabel}}
    </span>
@endif