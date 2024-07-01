@if(is_array($value))
    <div class="">
        <table class="ub-table mini border" style="width:{{$width-40}}px;white-space:normal;word-break:break-all;">
            @foreach($value as $k=>$v)
                <tr>
                    <td width="50" class="ub-text-truncate">{{$k}}</td>
                    <td>
                        @if(is_array($v))
                            {{\ModStart\Core\Util\SerializeUtil::jsonEncode($v)}}
                        @else
                            {{$v}}
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
@elseif(!empty($value))
    <pre style="margin:0;line-height:1rem;overflow:auto;width:{{$width}};">{{\ModStart\Core\Util\SerializeUtil::jsonEncodePretty($value)}}</pre>
@endif
