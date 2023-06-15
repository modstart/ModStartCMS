@if(is_array($value))
    <div class="tw-bg-gray-50 tw-p-1 tw-rounded">
        <table class="ub-table mini">
            @if(empty($fields))
                @foreach($value as $k=>$v)
                    <tr>
                        <td width="1%">{{$k}}</td>
                        <td>{{$v}}</td>
                    </tr>
                @endforeach
            @else
                @foreach($fields as $f)
                    <tr>
                        <td width="1%" class="ub-text-truncate">
                            <div data-tip-popover="{{$f['name']}}"
                                  class="tw-bg-gray-200 tw-px-2 tw-rounded-2xl">{{$f['title']}}</div>
                        </td>
                        <td>
                            @if($f['type']=='switch')
                                @if(!empty($value[$f['name']]))
                                    <i class="iconfont icon-check-simple"></i>
                                @else
                                    <i class="iconfont icon-close"></i>
                                @endif
                            @elseif($f['type']=='icon')
                                @if(!empty($value[$f['name']]))
                                    <i class="{{$value[$f['name']]}}"></i>
                                @else
                                    <span class="ub-text-muted">-</span>
                                @endif
                            @else
                                {{isset($value[$f['name']])?$value[$f['name']]:'-'}}
                            @endif

                        </td>
                    </tr>
                @endforeach
            @endif
        </table>
    </div>
@elseif(!empty($value))
    <pre style="margin:0;line-height:1rem;overflow:auto;width:{{$width}};">{{json_encode($value,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)}}</pre>
@endif
