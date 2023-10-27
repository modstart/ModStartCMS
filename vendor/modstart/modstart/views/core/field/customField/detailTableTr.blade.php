@if(!empty($fields))
@foreach($fields as $f)
    @if(!empty($f))
    <tr>
        <td>{{$f['title']}}</td>
        <td>
            @if(empty($value[$f['_name']]))
                <span class="ub-text-muted">-</span>
            @else
                @if($f['type']==\ModStart\Field\Type\CustomFieldType::TYPE_TEXT)
                    {{$value[$f['_name']]}}
                @elseif($f['type']==\ModStart\Field\Type\CustomFieldType::TYPE_RADIO)
                    {{$value[$f['_name']]}}
                @elseif($f['type']==\ModStart\Field\Type\CustomFieldType::TYPE_FILE)
                    <div>
                        <a href="{{$value[$f['_name']]}}" target="_blank">
                            <i class="iconfont icon-file"></i>
                            {{$value[$f['_name']]}}
                        </a>
                    </div>
                @elseif($f['type']==\ModStart\Field\Type\CustomFieldType::TYPE_FILES)
                    @if(!empty($value[$f['_name']]) && is_array($value[$f['_name']]))
                        @foreach($value[$f['_name']] as $v)
                            <a href="{{$v}}" target="_blank">
                                <i class="iconfont icon-file"></i>
                                {{$v}}
                            </a>
                        @endforeach
                    @else
                        <span class="ub-text-muted">-</span>
                    @endif
                @else
                    暂未支持 {{$f['type']}}
                    <code>{{\ModStart\Core\Util\SerializeUtil::jsonEncode($f)}}</code>
                @endif
            @endif
        </td>
    </tr>
    @endif
@endforeach
@endif
