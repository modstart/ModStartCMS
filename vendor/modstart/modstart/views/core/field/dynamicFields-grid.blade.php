@if(is_array($value))
    <div class="tw-bg-gray-50 tw-p-1 tw-rounded">
        <table class="ub-table mini">
            @foreach($value as $f)
                <tr>
                    <td width="1%" class="ub-text-truncate">
                        <div data-tip-popover="{{$f['name']?$f['name']:'-'}}"
                              class="tw-bg-gray-200 tw-px-2 tw-rounded-2xl">{{$f['title']?$f['title']:'-'}}</div>
                    </td>
                    <td>
                        {{\ModStart\Core\Type\TypeUtil::name(\ModStart\Field\Type\DynamicFieldsType::class,$f['type'])}}
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
@elseif(!empty($value))
    <pre style="margin:0;line-height:1rem;overflow:auto;width:{{$width}};">{{json_encode($value,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)}}</pre>
@endif
