@if($autoColor)
    <span class="ub-text-{{$value>0?'success':'danger'}}">{{($signShow&&($value>0))?'+':''}}{{$value}}</span>
@else
    {{($signShow&&($value>0))?'+':''}}{{$value}}
@endif
