@if(!empty($value))
    <a href="{{$value}}" target="_blank">
        <i class="iconfont icon-file"></i>
        {{L('View File')}}
    </a>
@else
    <span class="ub-text-muted">-</span>
@endif
