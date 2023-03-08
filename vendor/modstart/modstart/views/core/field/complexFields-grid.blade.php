@if(is_array($value))
    <div class="tw-bg-gray-100 tw-p-1 tw-rounded">
        <table class="ub-table mini" style="width:{{$width-40}}px;white-space:normal;word-break:break-all;">
            @foreach($value as $k=>$v)
                <tr>
                    <td width="50">{{$k}}</td>
                    <td>{{$v}}</td>
                </tr>
            @endforeach
        </table>
    </div>
@elseif(!empty($value))
    <pre style="margin:0;line-height:1rem;overflow:auto;width:{{$width}};">{{json_encode($value,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)}}</pre>
@endif
