@if(!empty($value))
    <a href="{{$value}}" target="_blank">{{L('View File')}}</a>
@else
    <span class="ub-text-muted">-</span>
@endif
