@if(!empty($value))
    @if($jsonMode=='api')
        <div>
            <span class="ub-tag primary">{{$value['method']}}</span>
            <code>{{$value['url']}}</code>
        </div>
    @else
        <pre
            style="margin:0;line-height:1rem;overflow:auto; @if(!empty($showMaxHeight)) max-height:{{$showMaxHeight}}; @endif" class="tw-bg-white ub-scroll-bar-mini">{{\ModStart\Core\Util\SerializeUtil::jsonEncodePretty($value)}}</pre>
    @endif
@else
    <span class="ub-text-muted">-</span>
@endif
