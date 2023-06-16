@if(!empty($fields))
@foreach($fields as $f)
    <tr>
        <td>{{$f['title']}}</td>
        <td>
            @if(empty($valueObject[$f['name']]))
                <span class="ub-text-muted">-</span>
            @else
                @if($f['type']==\ModStart\Field\Type\DynamicFieldsType::TYPE_TEXT)
                    {{$valueObject[$f['name']]}}
                @elseif($f['type']==\ModStart\Field\Type\DynamicFieldsType::TYPE_NUMBER)
                    {{$valueObject[$f['name']]}}
                @elseif($f['type']==\ModStart\Field\Type\DynamicFieldsType::TYPE_SWITCH)
                    {{$valueObject[$f['name']]}}
                @elseif($f['type']==\ModStart\Field\Type\DynamicFieldsType::TYPE_RADIO)
                    {{$valueObject[$f['name']]}}
                @elseif($f['type']==\ModStart\Field\Type\DynamicFieldsType::TYPE_SELECT)
                    {{$valueObject[$f['name']]}}
                @elseif($f['type']==\ModStart\Field\Type\DynamicFieldsType::TYPE_CHECKBOX)
                    @foreach($valueObject[$f['name']] as $v)
                        <span class="ub-tag">{{$v}}</span>
                    @endforeach
                @elseif($f['type']==\ModStart\Field\Type\DynamicFieldsType::TYPE_FILE)
                    <div>
                        <a href="{{$valueObject[$f['name']]}}" target="_blank">
                            <i class="iconfont icon-file"></i>
                            {{$valueObject[$f['name']]}}
                        </a>
                    </div>
                @elseif($f['type']==\ModStart\Field\Type\DynamicFieldsType::TYPE_FILES)
                    @foreach($valueObject[$f['name']] as $v)
                        <a href="{{$v}}" target="_blank">
                            <i class="iconfont icon-file"></i>
                            {{$v}}
                        </a>
                    @endforeach
                @else
                    暂未支持 {{$f['type']}}
                    <code>{{json_encode($f,JSON_UNESCAPED_UNICODE)}}</code>
                @endif
            @endif
        </td>
    </tr>
@endforeach
@endif
