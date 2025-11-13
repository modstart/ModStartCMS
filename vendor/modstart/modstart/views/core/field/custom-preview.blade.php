@if(!empty($value))
    <pre
        style="margin:0;line-height:1rem;overflow:auto; @if(!empty($showMaxHeight)) max-height:{{$showMaxHeight}}; @endif" class="tw-bg-white ub-scroll-bar-mini">{{\ModStart\Core\Util\SerializeUtil::jsonEncodePretty($value)}}</pre>
@else
    <span class="ub-text-muted">-</span>
@endif
