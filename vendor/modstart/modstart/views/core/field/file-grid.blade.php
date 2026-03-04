@if(!empty($value))
    <a href="{{$value}}" target="_blank">
        <i class="iconfont icon-file"></i>
        {{L('ViewFile')}}
    </a>
@else
    <span class="ub-text-muted">-</span>
@endif
